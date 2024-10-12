package main

import (
	"fmt"
	"net"
	"strings"
)

// Wraps the read function
func Read(conn net.Conn, prompt, blocked string, maximumLen int) (string, error) {
	return read(conn, prompt, blocked, maximumLen, make([]string, 0))
}

// Wraps the read function
func ReadWithHistory(conn net.Conn, prompt, blocked string, maximumLen int, history []string) (string, error) {
	return read(conn, prompt, blocked, maximumLen, history)
}

// Read will act as the reader for taking inputs from master connections
func read(conn net.Conn, prompt, blocked string, maximumLen int, history []string) (string, error) {
	if _, err := conn.Write([]byte(prompt)); err != nil {
		return "", err
	}
	
	var message []string = make([]string, 0)
	if _, err := conn.Write([]byte{255, 251, 1, 255, 251, 3, 255, 252, 34}); err != nil {
		return "", err
	}

	pos := len(history)

	for {
		var buf []byte = make([]byte, 1)
		_, err := conn.Read(buf)
		if err != nil {
			return "", err
		}

		switch buf[len(buf) - 1] { // 0
		case 16, 3, 2, 1, 11, 12, 5, 8, 31, 255, 251, 39, 24, 253, 10:
			continue

		case 127: // Backspace
			if len(message) <= 0 {
				continue
			}
			 
			message = message[:len(message)-1]
			if _, err := conn.Write([]byte{127}); err != nil {
				return "", err
			}

		case 13: // Enter
			if len(message) <= 0 {
				continue
			}

			if _, err := conn.Write([]byte("\r\n")); err != nil {
				return "", err
			}

			return strings.Join(message, ""), nil

		case 27: // Movement
			var buffer []byte = make([]byte, 5)
			if _, err := conn.Read(buffer); err != nil {
				return "", err
			}

			switch buffer[1] {
			case 65: // Up arrow
				if pos <= 0 {
					continue
				}

				pos--
				if _, err := conn.Write([]byte(fmt.Sprintf("\r\x1b[K%s%s", prompt, history[pos]))); err != nil {
					return "", err
				}

				message = strings.Split(history[pos], "")

			case 66: // Down arrow
				if pos + 1 >= len(history) {
					continue
				}

				pos++
				if _, err := conn.Write([]byte(fmt.Sprintf("\r\x1b[K%s%s", prompt, history[pos]))); err != nil {
					return "", err
				}

				message = strings.Split(history[pos], "")
			}
		
		default: // Safe input
			if len(message) + 1 > maximumLen {
				continue
			}

			var write string = string(buf[0])
			if len(blocked) > 0  {
				write = blocked
			}

			if _, err := conn.Write([]byte(fmt.Sprint(write))); err != nil {
				return "", err
			}

			message = append(message, string(buf[0]))
		}
	}
}