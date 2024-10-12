package paymentsapi

import (
	"api/core/database"
	"api/core/master/sessions"
	"api/core/models/server"
	"encoding/json"
	"fmt"
	"net/http"
	"strconv"
	"strings"
)

func init() {
	Route.NewSub(server.NewRoute("/status", func(w http.ResponseWriter, r *http.Request) {
		type Payment struct {
			ID            int     `json:"id"`
			Amount        int     `json:"amount"`
			CryptoAmount  float64 `json:"crypto_amount"`
			CryptoAddress string  `json:"crypto_address"`
			CryptoCoin    string  `json:"crypto_coin"`
			QrCode        string  `json:"qr_code"`
			Recieved      float64 `json:"recieved"`
			Status        string  `json:"status"`
			Date          int64   `json:"date"`
		}
		type Status struct {
			Status  string   `json:"status"`
			Message string   `json:"message"`
			Payment *Payment `json:"payment_info"`
		}
		switch strings.ToLower(r.Method) {
		case "post":
			ok, _ := sessions.IsLoggedIn(w, r)
			if !ok {
				http.Redirect(w, r, "/login", http.StatusTemporaryRedirect)
				return
			}
			id, _ := strconv.Atoi(r.PostFormValue("payment_id"))
			payment, err := database.Container.GetSale(id)
			if err != nil {
				json.NewEncoder(w).Encode(&Status{Status: "error", Message: err.Error(), Payment: &Payment{}})
				return
			}
			json.NewEncoder(w).Encode(&Status{Status: "success", Message: "transaction information", Payment: &Payment{
				ID:            payment.ID,
				Amount:        payment.Amount,
				CryptoAmount:  payment.CryptoAmount,
				CryptoAddress: payment.Address,
				CryptoCoin:    payment.Coin,
				QrCode:        "https://api.qrserver.com/v1/create-qr-code/?size=280x280&data=" + payment.Coin + ":" + payment.Address + "?amount=" + fmt.Sprint(payment.CryptoAmount),
				Recieved:      payment.Recieved,
				Status:        payment.Status,
				Date:          payment.Date,
			}})
		}
	}))
}
