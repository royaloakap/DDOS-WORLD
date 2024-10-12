package bot

import (
	"Telegram/database"
	"fmt"

	tgbotapi "github.com/go-telegram-bot-api/telegram-bot-api/v5"
)

var Bot *tgbotapi.BotAPI
var err error

func Start() {
	Bot, err = tgbotapi.NewBotAPI(database.Config.Token)
	database.CheckError(err)
	go Listen()
	go database.ListenURL(Bot)
	fmt.Println("Bot Started")
}
