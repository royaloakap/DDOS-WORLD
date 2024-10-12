package paymentsapi

import (
	"api/core/database"
	"api/core/master/sessions"
	"api/core/models/plans"
	"api/core/models/server"
	"encoding/json"
	"fmt"
	"net/http"
	"strings"
)

func init() {
	Route.NewSub(server.NewRoute("/buy", func(w http.ResponseWriter, r *http.Request) {
		type Status struct {
			Status  string `json:"status"`
			Message string `json:"message"`
		}
		switch strings.ToLower(r.Method) {
		case "post":
			ok, user := sessions.IsLoggedIn(w, r)
			if !ok {
				http.Redirect(w, r, "/login", http.StatusTemporaryRedirect)
				return
			}
			planVal := r.PostFormValue("plan_name")
			plan := plans.Plans[planVal]

			if plan != nil {
				if user.Balance >= plan.Price {
					database.Container.UpdateUserPlan(user.User, plan)
					json.NewEncoder(w).Encode(&Status{Status: "success", Message: "succesfully purchased " + fmt.Sprint(planVal) + ""})
					return
				} else {
					json.NewEncoder(w).Encode(&Status{Status: "error", Message: "Insufficient Balance!"})
					return
				}
			} else {
				json.NewEncoder(w).Encode(&Status{Status: "error", Message: "Invalid plan!"})
			}
			return
		}
	}))
}
