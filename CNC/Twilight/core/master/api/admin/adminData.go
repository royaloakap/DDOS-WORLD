package adminapi

import (
	"api/core/database"
	"api/core/master/sessions"
	"api/core/models/functions"
	"api/core/models/plans"
	"api/core/models/server"
	"net/http"
	"strings"
)

func init() {
	Route.NewSub(server.NewRoute("/data", func(w http.ResponseWriter, r *http.Request) {
		if strings.ToLower(r.Method) == "post" {
			ok, session := sessions.IsLoggedIn(w, r)
			if !ok {
				http.Redirect(w, r, "/login", http.StatusTemporaryRedirect)
				return
			}
			if !session.HasPermission("admin") {
				http.Redirect(w, r, "/dashboard", http.StatusTemporaryRedirect)
				return
			}
			type plan struct {
				Name string `json:"name"`
			}
			type user struct {
				Username string `json:"username"`
				ID       int    `json:"id"`
			}
			type Data struct {
				UserCount          int     `json:"userCount"`
				AttackCount        int     `json:"dailyAttackCount"`
				RunningAttackCount int     `json:"runningAttackCount"`
				ProfitCount        int     `json:"profitCount"`
				Users              []*user `json:"users"`
				Plans              []*plan `json:"plans"`
			}
			d := new(Data)
			d.UserCount = database.Container.Users()
			d.AttackCount = database.Container.DailyAttacks()
			d.RunningAttackCount = database.Container.GlobalRunning()
			d.ProfitCount = database.Container.Sales()
			d.Users = func() []*user {
				users, err := database.Container.GetUsers()
				if err != nil {
					return nil
				}
				var u []*user = make([]*user, 0)
				for _, us := range users {
					u = append(u, &user{Username: us.Username, ID: us.ID})
				}
				return u
			}()
			d.Plans = func() []*plan {
				var p []*plan = make([]*plan, 0)
				for name := range plans.Plans {
					p = append(p, &plan{Name: name})
				}
				return p
			}()

			functions.WriteJson(w, d)
		} else {
			w.Write([]byte("404 page not found"))
			w.WriteHeader(404)
		}
	}))
}
