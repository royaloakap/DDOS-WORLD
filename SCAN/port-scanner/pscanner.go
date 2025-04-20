package main

import (
    "fmt"
    "net"
    "os"
    "strconv"
)

func scanPort(ip string, port int) bool {
    target := fmt.Sprintf("%s:%d", ip, port)
    conn, err := net.Dial("tcp", target)
    if err != nil {
        return false
    }
    defer conn.Close()
    return true
}

func main() {
    args := os.Args[1:]
    if len(args) != 3 || args[1] != "-p" {
        fmt.Println("Usage: ./pscanner <IP> -p <port>")
        fmt.Println("Example: ./pscanner 87.5.72.86 -p 22 or all (Scan all ports)")
        return
    }

    ip := args[0]
    portArg := args[2]

    if portArg == "all" {
        for port := 1; port <= 65535; port++ {
            if scanPort(ip, port) {
                fmt.Printf("Port %d is open!\n", port)
            }
        }
    } else {
        port, err := strconv.Atoi(portArg)
        if err != nil {
            fmt.Println("Something is wrong.")
            return
        }

        if port < 1 || port > 65535 {
            fmt.Println("Error: Port must be in the range 1 to 65535")
            return
        }

        if scanPort(ip, port) {
            fmt.Printf("Port %d is open\n", port)
        } else {
            fmt.Printf("Port %d is closed\n", port)
        }
    }
}
