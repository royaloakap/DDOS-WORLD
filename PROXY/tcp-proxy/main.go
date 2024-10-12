package main

import (
	"bytes"
	"encoding/json"
	"flag"
	"fmt"
	"io"
	"log"
	"net"
	"net/http"
	"os"
	"strings"
	"sync"
	"time"
	"github.com/fatih/color"
	"golang.org/x/time/rate"
)

type Configuration struct {
	Proxies             []ProxyConfig  `json:"proxies"`
	Debug               bool           `json:"debug"`
	Telegram            TelegramConfig `json:"telegram"`
	RateLimitPerIP      int            `json:"rate_limit_per_ip"`
	BlockedSSHClients   []string       `json:"blocked_ssh_clients"`
	BlockDuration       int            `json:"block_duration_minutes"`
	MaxConnectionsPerIP int            `json:"max_connections_per_ip"`
}

type TelegramConfig struct {
	Enable bool   `json:"enable"`
	Token  string `json:"token"`
	ChatID string `json:"chat_id"`
}

type ProxyConfig struct {
	Name       string `json:"name"`
	ProxyIP    string `json:"proxy_ip"`
	ProxyPort  string `json:"proxy_port"`
	ServerIP   string `json:"server_ip"`
	ServerPort string `json:"server_port"`
}

type IPBlacklist struct {
	mu       sync.RWMutex
	blocked  map[string]time.Time
	limiters map[string]*rate.Limiter
}

var (
	configFilePath = flag.String("config", "config.json", "Path to the configuration file")
	blacklist      = &IPBlacklist{
		blocked:  make(map[string]time.Time),
		limiters: make(map[string]*rate.Limiter),
	}
	config Configuration
)

func main() {
	flag.Parse()

	config = loadConfig(*configFilePath)

	for _, proxy := range config.Proxies {
		go startProxy(proxy, config.Telegram)
	}

	select {}
}

func loadConfig(path string) Configuration {
	file, err := os.Open(path)
	if err != nil {
		log.Fatalf("[Error] Failed to open config file: %v", err)
	}
	defer file.Close()

	var config Configuration
	decoder := json.NewDecoder(file)
	err = decoder.Decode(&config)
	if err != nil {
		log.Fatalf("[Error] Failed to decode config JSON: %v", err)
	}

	return config
}

func startProxy(proxyCfg ProxyConfig, telegramCfg TelegramConfig) {
	proxyAddr := fmt.Sprintf("%s:%s", proxyCfg.ProxyIP, proxyCfg.ProxyPort)
	serverAddr := fmt.Sprintf("%s:%s", proxyCfg.ServerIP, proxyCfg.ServerPort)

	listener, err := net.Listen("tcp", proxyAddr)
	if err != nil {
		message := fmt.Sprintf("Failed to start proxy listener on %s: %v", proxyAddr, err)
		logError(message)
		sendTelegramLog(telegramCfg, message)
		return
	}
	defer listener.Close()

	message := fmt.Sprintf("Proxy '%s' started. Listening on %s, forwarding to %s", proxyCfg.Name, proxyAddr, serverAddr)
	logSuccess(message)
	sendTelegramLog(telegramCfg, message)

	for {
		clientConn, err := listener.Accept()
		if err != nil {
			logError(fmt.Sprintf("Failed to accept client connection: %v", err))
			continue
		}

		clientIP := clientConn.RemoteAddr().(*net.TCPAddr).IP.String()

		if isIPBlacklisted(clientIP) {
			logError(fmt.Sprintf("Blocked connection from blacklisted IP: %s", clientIP))
			clientConn.Close()
			continue
		}

		limiter := getLimiter(clientIP, config.RateLimitPerIP)
		if !limiter.Allow() {
			logError(fmt.Sprintf("Rate limit exceeded for IP: %s", clientIP))
			addIPToBlacklist(clientIP, config.BlockDuration)
			clientConn.Close()
			continue
		}

		if isClientBlocked(clientConn, config.BlockedSSHClients) {
			logError(fmt.Sprintf("Blocked connection from SSH client: %s", clientConn.RemoteAddr()))
			clientConn.Close()
			continue
		}

		go handleClient(clientConn, serverAddr, telegramCfg)
	}
}

func handleClient(clientConn net.Conn, serverAddr string, telegramCfg TelegramConfig) {
	defer clientConn.Close()

	serverConn, err := net.Dial("tcp", serverAddr)
	if err != nil {
		message := fmt.Sprintf("Failed to connect to server %s: %v", serverAddr, err)
		logError(message)
		sendTelegramLog(telegramCfg, message)
		return
	}
	defer serverConn.Close()

	go copyData(clientConn, serverConn)
	go copyData(serverConn, clientConn)
}

func copyData(dst, src net.Conn) {
	_, err := io.Copy(dst, src)
	if err != nil {
		if strings.Contains(err.Error(), "use of closed network connection") {
			return
		}
		logError(fmt.Sprintf("Error copying data: %v", err))
	}
}

func sendTelegramLog(telegramCfg TelegramConfig, message string) {
	if !telegramCfg.Enable || telegramCfg.Token == "" || telegramCfg.ChatID == "" {
		return
	}

	url := fmt.Sprintf("https://api.telegram.org/bot%s/sendMessage", telegramCfg.Token)
	data := map[string]string{
		"chat_id": telegramCfg.ChatID,
		"text":    message,
	}
	jsonData, _ := json.Marshal(data)

	resp, err := http.Post(url, "application/json", bytes.NewBuffer(jsonData))
	if err != nil {
		logError(fmt.Sprintf("Failed to send log to Telegram: %v", err))
		return
	}
	defer resp.Body.Close()

	if resp.StatusCode != http.StatusOK {
		logError(fmt.Sprintf("Failed to send log to Telegram, status code: %d", resp.StatusCode))
	}
}

func addIPToBlacklist(ip string, durationMinutes int) {
	blacklist.mu.Lock()
	defer blacklist.mu.Unlock()

	blacklist.blocked[ip] = time.Now().Add(time.Duration(durationMinutes) * time.Minute)
	logError(fmt.Sprintf("IP %s has been added to the blacklist for %d minutes", ip, durationMinutes))
}

func isIPBlacklisted(ip string) bool {
	blacklist.mu.RLock()
	defer blacklist.mu.RUnlock()

	expiryTime, exists := blacklist.blocked[ip]
	if !exists {
		return false
	}
	if time.Now().After(expiryTime) {
		delete(blacklist.blocked, ip) // Unblock IP after expiration
		return false
	}
	return true
}

func getLimiter(ip string, limit int) *rate.Limiter {
	blacklist.mu.Lock()
	defer blacklist.mu.Unlock()

	if _, exists := blacklist.limiters[ip]; !exists {
		blacklist.limiters[ip] = rate.NewLimiter(rate.Limit(limit), 1)
	}
	return blacklist.limiters[ip]
}

func isClientBlocked(clientConn net.Conn, blockedClients []string) bool {
	clientAddr := clientConn.RemoteAddr().String()
	for _, client := range blockedClients {
		if strings.Contains(strings.ToLower(clientAddr), strings.ToLower(client)) {
			return true
		}
	}
	return false
}

func logSuccess(message string) {
	color.New(color.FgGreen).Printf("[Success] %s\n", message)
}

func logError(message string) {
	color.New(color.FgRed).Printf("[Error] %s\n", message)
}
