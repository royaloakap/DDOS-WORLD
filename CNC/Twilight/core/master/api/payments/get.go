package paymentsapi

import (
	"api/core/database"
	"api/core/master/sessions"
	"api/core/models/server"
	"encoding/json"
	"net/http"
	"strings"
)

func init() {
	Route.NewSub(server.NewRoute("/history", func(w http.ResponseWriter, r *http.Request) {
		type Payment struct {
			ID     int    `json:"id"`
			Amount int    `json:"amount"`
			Status string `json:"status"`
			Coin   string `json:"coin"`
			Date   int64  `json:"date"`
		}
		type Status struct {
			Status  string     `json:"status"`
			Message string     `json:"message"`
			Sales   []*Payment `json:"payments"`
		}
		switch strings.ToLower(r.Method) {
		case "post":
			ok, user := sessions.IsLoggedIn(w, r)
			if !ok {
				http.Redirect(w, r, "/login", http.StatusTemporaryRedirect)
				return
			}
			payments, err := database.Container.GetUserHistory(user.User)
			if err != nil {
				json.NewEncoder(w).Encode(&Status{Status: "error", Message: err.Error(), Sales: []*Payment{}})
				return
			}
			var sales []*Payment
			for _, payment := range payments {
				sales = append(sales, &Payment{
					ID:     payment.ID,
					Amount: payment.Amount,
					Status: payment.Status,
					Coin:   payment.Coin,
					Date:   payment.Date,
				})
			}
			json.NewEncoder(w).Encode(&Status{Status: "success", Message: "transaction information", Sales: sales})
		}
	}))
}
