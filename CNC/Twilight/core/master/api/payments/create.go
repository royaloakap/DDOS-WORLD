package paymentsapi

import (
	"api/core/database"
	"api/core/master/sessions"
	"api/core/models/sellix"
	"api/core/models/server"
	"api/core/models"
	"encoding/json"
	"fmt"
	"net/http"
	"strconv"
	"strings"
	"time"
)

func init() {
	Route.NewSub(server.NewRoute("/create", func(w http.ResponseWriter, r *http.Request) {
		type Status struct {
			Status  string `json:"status"`
			Message string `json:"message"`
			ID      int    `json:"id"`
		}
		switch strings.ToLower(r.Method) {
		case "post":
			ok, user := sessions.IsLoggedIn(w, r)
			if !ok {
				http.Redirect(w, r, "/login", http.StatusTemporaryRedirect)
				return
			}
			Currency := r.PostFormValue("coin")
			Coupon := r.PostFormValue("coupon")
			Amount, err := strconv.Atoi(r.PostFormValue("amount"))
			if err != nil {
				json.NewEncoder(w).Encode(&Status{Status: "error", Message: err.Error(), ID: 0})
				return
			}
			payment := sellix.Manager.NewPayment(Amount, "USD", "Twilight Payment", models.Config.Autobuy.Email, Currency, Coupon, r)
			response, err := sellix.Manager.CreatePayment(payment)
			if err != nil {
				json.NewEncoder(w).Encode(&Status{Status: "error", Message: err.Error(), ID: 0})
				return
			}
			id, err := database.Container.NewSale(&database.Sale{
				UniqID:       response.Data.Invoice.Uniqid,
				Amount:       Amount,
				Parent:       user.ID,
				Coin:         Currency,
				Status:       "waiting",
				Product:      "Recharge for " + user.Username + " " + fmt.Sprintf("%d$", Amount) + "",
				Date:         time.Now().Unix(),
				Address:      response.Data.Invoice.CryptoAddress,
				CryptoAmount: response.Data.Invoice.CryptoAmount,
			})
			if err != nil {
				json.NewEncoder(w).Encode(&Status{Status: "error", Message: err.Error(), ID: 0})
				return
			}
			json.NewEncoder(w).Encode(&Status{Status: "success", Message: "succesfully created invoice " + fmt.Sprint(response.Data.Invoice.Uniqid) + "", ID: id})
			return
		}
	}))
}
