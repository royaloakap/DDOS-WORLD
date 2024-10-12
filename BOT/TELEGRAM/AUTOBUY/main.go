package main

import (
	"Telegram/bot"
	"Telegram/database"
	"fmt"
)

func main() {
	database.Connect()
	fmt.Println("Connected to database")
	database.LoadConfig()
	fmt.Println("Loaded config")
	database.LoadPlans()
	fmt.Println("Loaded", len(database.Plans.Plan), "plans")
	database.LoadUsers()
	fmt.Println("Loaded", len(database.Users), "users")
	bot.Start()
	fmt.Println("Bot is running...")
	select {}
}
