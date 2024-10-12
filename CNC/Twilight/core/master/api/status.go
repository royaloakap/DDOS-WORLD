package api

import (
	"api/core/database"
	"api/core/models/functions"
	"api/core/models/server"
	"encoding/json"
	"net/http"
	"time"
)

func init() {
	Route.NewSub(server.NewRoute("/status", func(w http.ResponseWriter, r *http.Request) {
		type attack struct {
			ID     int
			Target string
			Method string
			Expiry int
		}
		type status struct {
			Status  int    `json:"status"`
			Message string `json:"message"`
			Account struct {
				Username string `json:"username"`
				Running  int    `json:"running"`
				Slots    int    `json:"slots"`
			} `json:"account"`
			Network struct {
				Running int `json:"networkRunning"`
				Slots   int `json:"networkSlots"`
			} `json:"network"`
			Attacks []*attack `json:"attacks"`
		}

		key, ok := functions.GetKey(w, r)
		if !ok {
			return
		}
		ongoing, err := database.Container.GetRunning(key)
		if err != nil {
			json.NewEncoder(w).Encode(map[string]any{"error": true, "message": "failed to fetch running attacks!"})
			return
		}

		var attacks []*attack
		for _, atk := range ongoing {
			attacks = append(attacks, &attack{
				ID:     atk.ID,
				Target: atk.Target,
				Method: atk.Name,
				Expiry: (int(atk.Created) + atk.Duration) - int(time.Now().Unix()),
			})
		}
		functions.WriteJson(w, status{
			Status:  200,
			Message: "attacks information",
			Account: struct {
				Username string "json:\"username\""
				Running  int    "json:\"running\""
				Slots    int    "json:\"slots\""
			}{
				Username: key.Username,
				Running:  len(ongoing),
				Slots:    key.Concurrents,
			},
			Network: struct {
				Running int "json:\"networkRunning\""
				Slots   int "json:\"networkSlots\""
			}{
				Running: database.Container.GlobalRunning(),
				Slots:   30,
			},
			Attacks: attacks,
		})
	}))
}
