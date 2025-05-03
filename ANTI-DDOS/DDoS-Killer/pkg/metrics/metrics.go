package alert

import (
	"fmt"
	"log"
	"time"
	"ddos-detector/pkg/detector"
	"github.com/go-telegram-bot-api/telegram-bot-api/v5"
)

func SendAlert(token string, chatID int64, anomaly detector.Anomaly) error {
	bot, err := tgbotapi.NewBotAPI(token)
	if err != nil {
		return fmt.Errorf("failed to initialize Telegram bot: %v", err)
	}

	msg := tgbotapi.NewMessage(chatID, fmt.Sprintf(
		"🚨 DDoS Attack Detected!\nIP: %s\nScore: %.2f\nTime: %s",
		anomaly.SourceIP, anomaly.Score, anomaly.Timestamp.Format(time.RFC3339),
	))

	_, err = bot.Send(msg)
	if err != nil {
		return fmt.Errorf("failed to send Telegram alert: %v", err)
	}

	log.Printf("Alert sent for IP: %s", anomaly.SourceIP)
	return nil
}