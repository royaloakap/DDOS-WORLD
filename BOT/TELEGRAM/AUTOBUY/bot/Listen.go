package bot

import (
	"Telegram/database"
	"Telegram/structs"
	"log"
	"strconv"
	"strings"
	"time"

	tgbotapi "github.com/go-telegram-bot-api/telegram-bot-api/v5"
)

func Listen() {
	updateConfig := tgbotapi.NewUpdate(0)
	updateConfig.Timeout = 60
	updates := Bot.GetUpdatesChan(updateConfig)
	if err != nil {
		log.Fatalf("Error setting up update channel: %v", err)
	}
	for update := range updates {
		if update.Message == nil {
			continue
		}
		if update.Message.IsCommand() {
			handleCommand(Bot, update.Message.Chat.ID, update.Message.Command(), update.Message.CommandArguments(), update.Message)
		}
	}
}

func handleCommand(bot *tgbotapi.BotAPI, chatID int64, command string, arguments string, message *tgbotapi.Message) {
	switch command {
	case "start", "help":
		message := tgbotapi.NewMessage(chatID, "👋 This is our new Telebot! \r\n")
		bot.Send(message)
	case "plans":
		msg := "Plans Available:\r\n"
		for _, plan := range database.Plans.Plan {
			price := strconv.Itoa(int(plan.Price))
			time := strconv.Itoa(plan.MaxTime)
			msg += " - " + plan.Name + "\r\n"
			msg += "  Description: " + plan.Description + "\r\n"
			msg += "  Price: " + price + "$ \r\n"
			msg += "  MaxTime: " + time + "\r\n"
			msg += "  Concurrents: " + strconv.Itoa(plan.MaxCons) + "\r\n"
			msg += "\r\n\r\n"
		}
		message := tgbotapi.NewMessage(chatID, msg)
		bot.Send(message)
	case "gentoken":
		args := strings.Split(arguments, " ")
		if len(args) != 2 {
			message := tgbotapi.NewMessage(chatID, "Usage: /gentoken [plan] [days]")
			bot.Send(message)
			return
		}
		plan := args[0]
		days, err := strconv.Atoi(args[1])
		database.CheckError(err)
		token := database.GenerateToken(plan, days)
		if token == "" {
			message := tgbotapi.NewMessage(chatID, "No such Plan available.\r\nPlease check /plans")
			bot.Send(message)
			return
		}
		message := tgbotapi.NewMessage(chatID, "Token: "+token)
		bot.Send(message)
	case "attack":
		args := strings.Split(arguments, " ")
		if len(args) != 4 {
			message := tgbotapi.NewMessage(chatID, "Usage: /attack [host] [port] [time] [method]")
			bot.Send(message)
			return
		}
		host := args[0]
		port, err := strconv.Atoi(args[1])
		database.CheckError(err)
		dur, err := strconv.Atoi(args[2])
		database.CheckError(err)
		method := args[3]
		if database.CheckBlacklist(host) {
			message := tgbotapi.NewMessage(chatID, "Host is blacklisted")
			bot.Send(message)
			return
		}
		user := database.SelectUser(strconv.FormatInt(message.From.ID, 10))
		if user.Plan.Name == "" {
			message := tgbotapi.NewMessage(chatID, "You need to buy a plan first. Check /plans and /buy")
			bot.Send(message)
			return
		}
		if user.Plan.MaxTime < dur {
			message := tgbotapi.NewMessage(chatID, "Your Time exceeds your plan's max time")
			bot.Send(message)
			return
		}
		for _, attack := range database.Running {
			if attack.From == user.ID && attack.Host == host {
				message := tgbotapi.NewMessage(chatID, "You already have an attack running")
				bot.Send(message)
				return
			}
		}

		var count int
		count = 0
		for _, attack := range database.Running {
			if attack.From == user.ID {
				count++
			}
		}
		if count >= user.Plan.MaxCons {
			message := tgbotapi.NewMessage(chatID, "You have reached your max concurrent attacks")
			bot.Send(message)
			return
		}

		if database.CheckExpired(user.ID) {
			message := tgbotapi.NewMessage(chatID, "Your plan has expired")
			bot.Send(message)
			return
		}
		if user.Banned {
			message := tgbotapi.NewMessage(chatID, "You are banned")
			bot.Send(message)
			return
		}

		for _, m := range database.Config.Method {
			if m.Name == method {
				database.SendAPIs(m, host, port, dur, user)
				go func() {
					database.Running = append(database.Running, structs.Attack{From: user.ID, Host: host, Port: port, Time: dur})
					time.Sleep(time.Duration(dur) * time.Second)
					for i, attack := range database.Running {
						if attack.From == user.ID {
							database.Running = append(database.Running[:i], database.Running[i+1:]...)
							break
						}
					}
				}()
			}
		}

		msg := " 🚀 Attack Sent 🚀\r\n"
		msg += "Host: " + host + "\r\n"
		msg += "Port: " + strconv.Itoa(port) + "\r\n"
		msg += "Time: " + strconv.Itoa(dur) + "\r\n"
		msg += "Cons: " + strconv.Itoa(count+1) + " / " + strconv.Itoa(user.Plan.MaxCons) + "\r\n"
		msg += "Method: " + method + "\r\n"
		_msg := tgbotapi.NewMessage(chatID, msg)
		bot.Send(_msg)
		database.SendLog("User "+message.From.UserName+" started an attack on "+host+":"+strconv.Itoa(port)+" for "+strconv.Itoa(dur)+" seconds using "+method, bot)
	case "methods":
		msg := " 🖥️ Methods Available 🖥️\r\n"
		for _, method := range database.Config.Method {
			msg += " - " + method.Name + "\r\n"
			msg += "  Description: " + method.Description + "\r\n"
			msg += "\r\n\r\n"
		}
		message := tgbotapi.NewMessage(chatID, msg)
		bot.Send(message)
	case "buy":
		args := strings.Split(arguments, " ")
		if len(args) != 1 {
			message := tgbotapi.NewMessage(chatID, "Usage: /buy [plan]")
			bot.Send(message)
			return
		}
		_plan := args[0]
		plan := database.GetPlan(_plan)
		if plan.Name == "" {
			message := tgbotapi.NewMessage(chatID, "Invalid Plan")
			bot.Send(message)
			return
		}
		msg := database.CreatePayment(plan.Name, message.From.UserName+"@gmail.com", plan.Price, chatID)
		_msg := tgbotapi.NewMessage(chatID, "Thank you for your purchase!\r\nAfter completing the order you will receive a Token.\r\nOnce the Order is processed your can redeem your Token by entering /redeem [token].\r\n\r\nPress the Button to Pay")
		_msg.ReplyMarkup = tgbotapi.NewInlineKeyboardMarkup(
			tgbotapi.NewInlineKeyboardRow(
				tgbotapi.NewInlineKeyboardButtonURL("Pay", msg),
			),
		)
		bot.Send(_msg)
		database.SendLog("User "+message.From.UserName+" ordered "+plan.Name, bot)
	case "redeem":
		args := strings.Split(arguments, " ")
		if len(args) != 1 {
			message := tgbotapi.NewMessage(chatID, "Usage: /redeem [token]")
			bot.Send(message)
			return
		}
		var _token structs.Token
		for _, token := range database.Tokens {
			if token.Token == args[0] {
				_token = token
			}
		}
		if _token.Token == "" {
			message := tgbotapi.NewMessage(chatID, "Invalid Token")
			bot.Send(message)
			return
		}
		user := database.SelectUser(strconv.FormatInt(message.From.ID, 10))
		if user.ID == "" {
			var user structs.User
			user.ID = strconv.FormatInt(message.From.ID, 10)
			user.Plan = _token.Plan
			user.Banned = false
			expr := time.Now().AddDate(0, 0, _token.Expiry).Format("02-01-2006")
			user.Expiry = expr
			database.InsertUser(user)
		} else {
			var user structs.User
			user.ID = strconv.FormatInt(message.From.ID, 10)
			if _token.Plan.Rank > user.Plan.Rank {
				msg := tgbotapi.NewMessage(chatID, "Failed to Redeem Plan. You already have a better Plan")
				bot.Send(msg)
				database.SendLog("User "+message.From.UserName+" Redeemed Plan "+_token.Plan.Name, bot)
				return
			}
			user.Plan = _token.Plan
			user.Banned = false
			userExpr, _ := time.Parse("02-01-2006", user.Expiry)
			userExpr = userExpr.AddDate(0, 0, _token.Expiry)
			user.Expiry = userExpr.Format("02-01-2006")
			database.UpdateUser(user)
		}
		msg := tgbotapi.NewMessage(chatID, "Plan Redeemed")
		bot.Send(msg)
		database.SendLog("User "+message.From.UserName+" Redeemed Plan "+_token.Plan.Name, bot)
	case "ban":
		args := strings.Split(arguments, " ")
		if len(args) != 1 {
			message := tgbotapi.NewMessage(chatID, "Usage: /ban [user]")
			bot.Send(message)
			return
		}
		user := database.SelectUser(args[0])
		if user.ID == "" {
			message := tgbotapi.NewMessage(chatID, "Invalid User")
			bot.Send(message)
			return
		}
		user.Banned = true
		database.UpdateUser(user)
		message := tgbotapi.NewMessage(chatID, "User Banned")
		bot.Send(message)
	case "unban":
		args := strings.Split(arguments, " ")
		if len(args) != 1 {
			message := tgbotapi.NewMessage(chatID, "Usage: /unban [user]")
			bot.Send(message)
			return
		}
		user := database.SelectUser(args[0])
		if user.ID == "" {
			message := tgbotapi.NewMessage(chatID, "Invalid User")
			bot.Send(message)
			return
		}
		user.Banned = false
		database.UpdateUser(user)
		message := tgbotapi.NewMessage(chatID, "User Unbanned")
		bot.Send(message)
	case "users":
		msg := ""
		for _, user := range database.Users {
			msg += " - " + user.ID + "\r\n"
			msg += "  Plan: " + user.Plan.Name + "\r\n"
			msg += "  Expiry: " + user.Expiry + "\r\n"
			msg += "  Plan: " + user.Plan.Name + "\r\n"
			msg += "  Banned: " + strconv.FormatBool(user.Banned) + "\r\n"
			msg += "\r\n\r\n"
		}
		message := tgbotapi.NewMessage(chatID, msg)
		bot.Send(message)
	case "myinfo", "plan", "info":
		user := database.SelectUser(strconv.FormatInt(message.From.ID, 10))
		if user.ID == "" {
			message := tgbotapi.NewMessage(chatID, "You don't have a plan")
			bot.Send(message)
			return
		}
		maxtime := strconv.Itoa(user.Plan.MaxTime)
		msg := "    " + user.ID + "     \r\n"
		msg += "  Plan: " + user.Plan.Name + "\r\n"
		msg += "  Expiry: " + user.Expiry + "\r\n"
		msg += "  MaxTime: " + maxtime + "\r\n"
		msg += "\r\n\r\n"
		message := tgbotapi.NewMessage(chatID, msg)
		bot.Send(message)
	case "broadcast":
		for _, id := range database.Config.Admins {
			if strconv.FormatInt(message.From.ID, 10) == id {
				for _, user := range database.Users {
					_id, _ := strconv.ParseInt(user.ID, 10, 64)
					msg := tgbotapi.NewMessage(_id, arguments)
					bot.Send(msg)
				}
			}
		}
	case "admincmds", "ahelp":
		for _, id := range database.Config.Admins {
			if strconv.FormatInt(message.From.ID, 10) == id {
				gentoken := "gentoken"
				users := "users"
				ban := "ban"
				unban := "unban"
				message := tgbotapi.NewMessage(chatID, "👋  Welcome Back! 👋 \r\nHere are the commands available:\r\n/gentoken [plan] [days]\r\n/users\r\n/ban [user]\r\n/unban [user]")
				keyboard := tgbotapi.InlineKeyboardMarkup{
					InlineKeyboard: [][]tgbotapi.InlineKeyboardButton{{
						tgbotapi.InlineKeyboardButton{
							Text:         "Generate Token",
							CallbackData: &gentoken,
						},
						tgbotapi.InlineKeyboardButton{
							Text:         "View Users",
							CallbackData: &users,
						},

						tgbotapi.InlineKeyboardButton{
							Text:         "Ban User",
							CallbackData: &ban,
						},

						tgbotapi.InlineKeyboardButton{
							Text:         "Unban User",
							CallbackData: &unban,
						},
					},
					}}
				message.ReplyMarkup = keyboard
				bot.Send(message)
				return
			}
		}
		message := tgbotapi.NewMessage(chatID, "You are not an admin")
		bot.Send(message)
	case "clear":
		msg := tgbotapi.NewMessage(chatID, "Clearing Chat...")
		bot.Send(msg)
		for i := 0; i < 100; i++ {
			msg := tgbotapi.NewDeleteMessage(chatID, i)
			bot.Send(msg)
		}
		msg = tgbotapi.NewMessage(chatID, "Chat Cleared!")
		bot.Send(msg)
	case "default":
		message := tgbotapi.NewMessage(chatID, "Sorry, I don't understand that command.")
		bot.Send(message)
	}
}
