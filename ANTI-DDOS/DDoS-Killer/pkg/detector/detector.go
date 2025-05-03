package detector

import (
	"log"
	"time"
	"ddos-detector/pkg/sniffer"
)

type Anomaly struct {
	SourceIP  string
	Score     float64
	Timestamp time.Time
}

func Start(metricsChan <-chan sniffer.TrafficMetrics, anomalyChan chan<- Anomaly) {
	for metrics := range metricsChan {
		// Simple threshold-based anomaly detection
		// Replace with Random Forest or Neural Network in production
		if metrics.RPS > 1000 || metrics.SYNRate > 500 {
			for ip, count := range metrics.SourceIPs {
				if count > 100 { // Suspicious IP
					anomalyChan <- Anomaly{
						SourceIP:  ip,
						Score:     float64(count) / float64(metrics.RPS),
						Timestamp: metrics.Timestamp,
					}
					log.Printf("Anomaly detected: IP=%s, Score=%.2f", ip, float64(count)/float64(metrics.RPS))
				}
			}
		}
	}
}