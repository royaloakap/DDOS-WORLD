package dashboardapi

import (
	"api/core/master/sessions"
	"api/core/models/plans"
	"api/core/models/server"
	"encoding/json"
	"net/http"
	"strings"
)

func init() {
	Route.NewSub(server.NewRoute("/plans", func(w http.ResponseWriter, r *http.Request) {
		if strings.ToLower(r.Method) == "post" {
			ok, _ := sessions.IsLoggedIn(w, r)
			if !ok {
				http.Redirect(w, r, "/login", http.StatusTemporaryRedirect)
				return
			}
			type Plan struct {
				Name     string `json:"name"`
				Duration int    `json:"duration"`
				Conns    int    `json:"concurrents"`
				API      bool   `json:"api"`
				Expiry   int    `json:"expiry"`
				Price    int    `json:"price"`
			}

			type Plans struct {
				Status string  `json:"status"`
				PPlans []*Plan `json:"plans"`
			}

			var a Plans
			a.Status = "success"
			for Name, plan := range plans.Plans {
				a.PPlans = append(a.PPlans, &Plan{
					Name:     Name,
					Duration: plan.Duration,
					Conns:    plan.Conns,
					API:      plan.API,
					Expiry:   plan.Expiry,
					Price:    plan.Price,
				})
			}
			json.NewEncoder(w).Encode(a)
		} else {
			w.Write([]byte("404 page not found"))
			w.WriteHeader(404)
		}
	}))
}
