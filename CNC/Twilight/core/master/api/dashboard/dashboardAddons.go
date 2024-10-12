package dashboardapi

import (
	"api/core/models/plans"
	"api/core/models/server"
	"encoding/json"
	"net/http"
	"strings"
)

func init() {
	Route.NewSub(server.NewRoute("/addons", func(w http.ResponseWriter, r *http.Request) {
		if strings.ToLower(r.Method) == "post" {
			type Addons struct {
				Name     string  `json:"name"`
				Value    int     `json:"value"`
				Expiry   int     `json:"expiry"`
				Price    int	 `json:"price"`
			}

			type Addon struct {
				Status  string    `json:"status"`
				Addons  []*Addons `json:"addons"`
			}

			var a Addon
			a.Status = "success"
			for Name, plan := range plans.Addons {
				a.Addons = append(a.Addons, &Addons{
					Name:     Name,
					Expiry:   plan.Expiry,
					Value:	  plan.Value,
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
