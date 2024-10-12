package database

import (
	"Telegram/structs"
	"bytes"
	"encoding/json"
	"fmt"
	tgbotapi "github.com/go-telegram-bot-api/telegram-bot-api/v5"
	"io"
	"math/rand"
	"net/http"
	"os"

	"strconv"
	"strings"
	"time"
)

func CheckError(err error) {
	if err != nil {
		fmt.Println(err)
	}
}

func LoadConfig() {
	file, err := os.Open("config/config.json")
	CheckError(err)
	err = json.NewDecoder(file).Decode(&Config)
	CheckError(err)
}

func CheckBlacklist(host string) bool {
	for _, user := range Config.Blacklist {
		if user == host {
			return true
		}
	}
	return false
}

func CheckAdmin(ID string) bool {
	for _, user := range Config.Admins {
		if user == ID {
			return true
		}
	}
	return false
}

func LoadPlans() {
	file, err := os.Open("config/plans.json")
	CheckError(err)
	err = json.NewDecoder(file).Decode(&Plans)
	CheckError(err)
}

func CheckExpired(ID string) bool {
	user := SelectUser(ID)
	expiry, err := time.Parse("02-01-2006", user.Expiry)
	CheckError(err)
	if time.Now().After(expiry) {
		return true
	}
	return false
}

func GetPlan(plan string) structs.Plan {
	for _, p := range Plans.Plan {
		if p.Name == plan {
			return p
		}
	}
	return structs.Plan{}
}

func GenerateToken(plan string, expiry int) string {
	token := ""
	// Generate XXXX-XXXX-XXXX-XXXX
	for i := 0; i < 4; i++ {
		token += GenerateString(4) + "-"
	}
	token = strings.TrimSuffix(token, "-")
	if GetPlan(plan).Name == "" {
		return ""
	}
	Tokens = append(Tokens, structs.Token{Token: token, Plan: GetPlan(plan), Expiry: expiry})
	return token
}

func GenerateString(length int) string {
	rand.Seed(time.Now().UnixNano())
	chars := []rune("ABCDEFGHIJKLMNOPQRSTUVWXYZ")
	var buffer bytes.Buffer
	for i := 0; i < length; i++ {
		buffer.WriteRune(chars[rand.Intn(len(chars))])
	}
	return buffer.String()
}

func SendAPIs(method structs.Method, host string, port int, duration int, user structs.User) {
	if len(method.APIs) > 0 {
		for _, api := range method.APIs {
			go func(_api string, _host string, _port int, _duration int, _user structs.User) {
				_url := strings.NewReplacer("[host]", _host, "[port]", strconv.Itoa(_port), "[time]", strconv.Itoa(_duration)).Replace(_api)
				resp, err := http.Get(_url)
				CheckError(err)
				defer func(Body io.ReadCloser) {
					err := Body.Close()
					CheckError(err)
				}(resp.Body)
				_, err = io.ReadAll(resp.Body)
				CheckError(err)
			}(api, host, port, duration, user)
		}
	}
}

func ListenURL(bot *tgbotapi.BotAPI) {
	http.HandleFunc("/", func(w http.ResponseWriter, r *http.Request) {
		if r.Method == "POST" {
			var webhook structs.SellixWebhook
			err := json.NewDecoder(r.Body).Decode(&webhook)
			CheckError(err)
			if webhook.Event == "order:paid" {
				msg := GenerateToken(webhook.Data.CustomFields.Plan, 30)
				message := tgbotapi.NewMessage(webhook.Data.CustomFields.TelegramID, "Thank you for your purchase!\n\nYour token is: "+msg+"\n\nYou can redeem it by using /redeem")
				bot.Send(message)
			}
		}
		return
	})
	http.ListenAndServe(":4949", nil)
}

func CreatePayment(title string, email string, value float64, id int64) string {
	botID := strings.Split(Config.Token, ":")[0]
	sellixPayload := structs.SellixPayload{
		Title:    title,
		Email:    email,
		Value:    value,
		Currency: Config.Currency,
		Quantity: 1,
		Webhook:  Config.URL,
		CustomFields: structs.CustomField{
			TelegramID: id,
			Plan:       title,
		},
		Whitelabel: false,
		ReturnURL:  "https://web.telegram.org/a/#" + botID,
	}
	sellixPayloadJSON, err := json.Marshal(sellixPayload)
	CheckError(err)
	req, err := http.NewRequest("POST", "https://dev.sellix.io/v1/payments", bytes.NewBuffer(sellixPayloadJSON))
	CheckError(err)
	req.Header.Set("Authorization", "Bearer "+Config.SellixKey)
	req.Header.Set("Content-Type", "application/json")
	client := &http.Client{}
	resp, err := client.Do(req)
	CheckError(err)
	defer func(Body io.ReadCloser) {
		err := Body.Close()
		CheckError(err)
	}(resp.Body)
	body, err := io.ReadAll(resp.Body)
	CheckError(err)
	var sellixResponse structs.SellixResponse
	err = json.Unmarshal(body, &sellixResponse)
	CheckError(err)
	return sellixResponse.Data.URL
}

func SendLog(msg string, bot *tgbotapi.BotAPI) {
	for _, id := range Config.Admins {
		_id, err := strconv.ParseInt(id, 10, 64)
		CheckError(err)
		message := tgbotapi.NewMessage(_id, msg)
		bot.Send(message)
	}
}
