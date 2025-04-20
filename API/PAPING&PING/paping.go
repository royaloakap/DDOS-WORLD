package main

import (
	"bufio"
	"fmt"
	"math/rand"
	"net"
	"net/http"
	"os"
	"os/exec"
	"runtime"
	"strings"
	"sync"
	"time"

	"github.com/gin-gonic/gin"
)

var proxies []string
var mu sync.Mutex

func main() {
	err := loadProxies("proxy.txt")
	if err != nil {
		fmt.Printf("Failed to load proxies: %s\n", err)
		return
	}

	r := gin.Default()

	r.GET("/paping", func(c *gin.Context) {
		ip := c.Query("ip")
		port := c.Query("port")

		if ip == "" || port == "" {
			c.JSON(http.StatusBadRequest, gin.H{
				"status":  "error",
				"message": "Missing 'ip' or 'port' parameter",
			})
			return
		}

		results := []gin.H{}
		timeout := time.After(15 * time.Second)
		done := make(chan bool)

		go func() {
			end := time.Now().Add(10 * time.Second)
			for time.Now().Before(end) {
				proxy := getRandomProxy()
				status, err := checkPaping(ip, port, proxy)
				if err != nil {
					results = append(results, gin.H{
						"status": "down",
						"error":  err.Error(),
						"proxy":  proxy,
					})
					removeProxy(proxy)
				} else {
					results = append(results, gin.H{
						"status":        "up",
						"response_time": status,
						"proxy":         proxy,
					})
				}
				time.Sleep(1 * time.Second)
			}
			done <- true
		}()

		select {
		case <-timeout:
			c.JSON(http.StatusRequestTimeout, gin.H{
				"status":  "error",
				"message": "Request timed out after 15 seconds",
				"ip":      ip,
				"port":    port,
				"results": results,
			})
		case <-done:
			c.JSON(http.StatusOK, gin.H{
				"ip":      ip,
				"port":    port,
				"results": results,
			})
		}
	})

	r.GET("/ping", func(c *gin.Context) {
		ip := c.Query("ip")

		if ip == "" {
			c.JSON(http.StatusBadRequest, gin.H{
				"status":  "error",
				"message": "Missing 'ip' parameter",
			})
			return
		}

		results := []gin.H{}
		timeout := time.After(15 * time.Second)
		done := make(chan bool)

		go func() {
			end := time.Now().Add(10 * time.Second)
			for time.Now().Before(end) {
				proxy := getRandomProxy()
				status, err := checkPing(ip, proxy)
				if err != nil {
					results = append(results, gin.H{
						"status": "down",
						"error":  err.Error(),
						"proxy":  proxy,
					})
					removeProxy(proxy)
				} else {
					results = append(results, gin.H{
						"status":        "up",
						"response_time": status,
						"proxy":         proxy,
					})
				}
				time.Sleep(1 * time.Second)
			}
			done <- true
		}()

		select {
		case <-timeout:
			c.JSON(http.StatusRequestTimeout, gin.H{
				"status":  "error",
				"message": "Request timed out after 15 seconds",
				"ip":      ip,
				"results": results,
			})
		case <-done:
			c.JSON(http.StatusOK, gin.H{
				"ip":      ip,
				"results": results,
			})
		}
	})

	if err := r.Run(":80"); err != nil {
		fmt.Printf("Error starting server: %s\n", err)
	}
}

func loadProxies(filePath string) error {
	file, err := os.Open(filePath)
	if err != nil {
		return fmt.Errorf("error opening proxy file: %w", err)
	}
	defer file.Close()

	scanner := bufio.NewScanner(file)
	for scanner.Scan() {
		proxies = append(proxies, scanner.Text())
	}

	if err := scanner.Err(); err != nil {
		return fmt.Errorf("error reading proxy file: %w", err)
	}

	if len(proxies) == 0 {
		return fmt.Errorf("no proxies found in file")
	}

	return nil
}

func getRandomProxy() string {
	mu.Lock()
	defer mu.Unlock()

	if len(proxies) == 0 {
		return ""
	}
	rand.Seed(time.Now().UnixNano())
	return proxies[rand.Intn(len(proxies))]
}

func removeProxy(proxy string) {
	mu.Lock()
	defer mu.Unlock()

	for i, p := range proxies {
		if p == proxy {
			proxies = append(proxies[:i], proxies[i+1:]...)
			break
		}
	}
}
func checkPaping(ip, port, proxy string) (string, error) {
	start := time.Now()
	dialer := &net.Dialer{
		Timeout: 5 * time.Second,
	}
	var conn net.Conn
	var err error
	if proxy != "" {
		proxyConn, proxyErr := dialer.Dial("tcp", proxy)
		if proxyErr != nil {
			return "", fmt.Errorf("proxy unreachable (%s): %v", proxy, proxyErr)
		}
		defer proxyConn.Close()
		conn, err = dialer.Dial("tcp", fmt.Sprintf("%s:%s", ip, port))
	} else {
		conn, err = dialer.Dial("tcp", fmt.Sprintf("%s:%s", ip, port))
	}

	if err != nil {
		return "", fmt.Errorf("server unreachable (%s:%s): %v", ip, port, err)
	}
	defer conn.Close()
	elapsed := time.Since(start)
	return elapsed.String(), nil
}

func checkPing(ip, proxy string) (string, error) {
	var cmd *exec.Cmd
	start := time.Now()

	if runtime.GOOS == "windows" {
		cmd = exec.Command("ping", "-n", "1", ip)
	} else {
		cmd = exec.Command("ping", "-c", "1", ip)
	}

	if proxy != "" {
		cmd.Env = append(cmd.Env, fmt.Sprintf("HTTP_PROXY=http://%s", proxy))
		cmd.Env = append(cmd.Env, fmt.Sprintf("HTTPS_PROXY=http://%s", proxy))
	}

	output, err := cmd.CombinedOutput()
	if err != nil {
		return "", fmt.Errorf("ping failed: %w", err)
	}

	elapsed := time.Since(start)
	if strings.Contains(string(output), "time=") {
		return elapsed.String(), nil
	}

	return "", fmt.Errorf("no response Contact @Royaloakap")
}
