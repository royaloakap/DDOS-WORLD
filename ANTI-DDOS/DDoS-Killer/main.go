package main

import (
	"log"
	"net/http"
	"sync"
	"ddos-detector/pkg/alert"
	"ddos-detector/pkg/detector"
	"ddos-detector/pkg/metrics"
	"ddos-detector/pkg/mitigator"
	"ddos-detector/pkg/sniffer"
	"github.com/gorilla/websocket"
	"github.com/shirou/gopsutil/cpu"
	"github.com/shirou/gopsutil/mem"
	"github.com/spf13/viper"
	"time"
)

var (
	upgrader = websocket.Upgrader{
		ReadBufferSize:  1024,
		WriteBufferSize: 1024,
	}
	clients   = make(map[*websocket.Conn]bool)
	clientsMu sync.Mutex
)

type DashboardMetrics struct {
	RPS         float64            `json:"rps"`
	TrafficRate float64            `json:"traffic_rate"`
	CPUUsage    float64            `json:"cpu_usage"`
	MemoryUsage float64            `json:"memory_usage"`
	Anomalies   []detector.Anomaly `json:"anomalies"`
}

func main() {
	// Load configuration
	viper.SetConfigName("config")
	viper.AddConfigPath("./config")
	if err := viper.ReadInConfig(); err != nil {
		log.Fatalf("Error reading config: %v", err)
	}

	// Initialize metrics
	metrics.Init()

	// Initialize channels
	snifferChan := make(chan sniffer.TrafficMetrics)
	detectorChan := make(chan detector.Anomaly)
	trafficMetrics := sniffer.TrafficMetrics{}
	var anomalies []detector.Anomaly

	// Start sniffer
	go sniffer.Start(viper.GetString("network.interface"), snifferChan)

	// Start detector
	go detector.Start(snifferChan, detectorChan)

	// Handle anomalies
	go func() {
		for anomaly := range detectorChan {
			// Block malicious IPs
			if err := mitigator.BlockIP(anomaly.SourceIP); err != nil {
				log.Printf("Failed to block IP %s: %v", anomaly.SourceIP, err)
			}

			// Send alert
			if err := alert.SendAlert(viper.GetString("telegram.token"), viper.GetInt64("telegram.chat_id"), anomaly); err != nil {
				log.Printf("Failed to send alert: %v", err)
			}

			// Store anomaly for dashboard
			anomalies = append(anomalies, anomaly)
			if len(anomalies) > 10 { // Keep last 10 anomalies
				anomalies = anomalies[1:]
			}
		}
	}()

	// Collect metrics for dashboard
	go func() {
		for metrics := range snifferChan {
			trafficMetrics = metrics
		}
	}()

	// Serve static files
	http.Handle("/web/", http.StripPrefix("/web/", http.FileServer(http.Dir("web"))))

	// WebSocket handler
	http.HandleFunc("/ws", func(w http.ResponseWriter, r *http.Request) {
		conn, err := upgrader.Upgrade(w, r, nil)
		if err != nil {
			log.Printf("WebSocket upgrade failed: %v", err)
			return
		}

		clientsMu.Lock()
		clients[conn] = true
		clientsMu.Unlock()

		defer func() {
			clientsMu.Lock()
			delete(clients, conn)
			clientsMu.Unlock()
			conn.Close()
		}()

		for {
			// Send metrics every second
			cpuPercent, _ := cpu.Percent(time.Second, false)
			memStats, _ := mem.VirtualMemory()

			dashboardMetrics := DashboardMetrics{
				RPS:         trafficMetrics.RPS,
				TrafficRate: trafficMetrics.RPS * 1500, // Assume avg packet size ~1500 bytes
				CPUUsage:    cpuPercent[0],
				MemoryUsage: memStats.UsedPercent,
				Anomalies:   anomalies,
			}

			clientsMu.Lock()
			for client := range clients {
				err := client.WriteJSON(dashboardMetrics)
				if err != nil {
					log.Printf("WebSocket write failed: %v", err)
					delete(clients, client)
					client.Close()
				}
			}
			clientsMu.Unlock()

			time.Sleep(1 * time.Second)
		}
	})

	// Start HTTP server
	log.Fatal(http.ListenAndServe(":8080", nil))
}