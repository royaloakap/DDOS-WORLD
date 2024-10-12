package panelapi

import (
	"api/core/database"
	"api/core/master/sessions"
	"api/core/models/server"
	"encoding/json"
	"net/http"
	"strings"
	"time"
)

func init() {
	Route.NewSub(server.NewRoute("/running", func(w http.ResponseWriter, r *http.Request) {
		if strings.ToLower(r.Method) == "post" {
			ok, session := sessions.IsLoggedIn(w, r)
			if !ok {
				http.Redirect(w, r, "/login", http.StatusTemporaryRedirect)
				return
			}
			type flood struct {
				ID      int    `json:"id"`
				Target  string `json:"target"`
				Method  string `json:"method"`
				Port    int    `json:"port"`
				Time    int    `json:"time"`
				Stopped int    `json:"stopped"`
				Sent    int64  `json:"date_sent"`
				Expires int    `json:"expires"`
			}

			type status struct {
				Status string   `json:"status"`
				Data   []*flood `json:"data"`
			}
			var s = &status{
				Data: make([]*flood, 0),
			}
			attacks, err := database.Container.GetRunning(session.User)
			if err != nil {
				s.Status = "error"
				json.NewEncoder(w).Encode(s)
				return
			}
			for _, attack := range attacks {
				s.Data = append(s.Data, &flood{
					ID:      attack.ID,
					Target:  attack.Target,
					Method:  attack.Name,
					Port:    attack.Port,
					Time:    attack.Duration,
					Stopped: attack.Stopped,
					Sent:    attack.Created,
					Expires: (int(attack.Created) + attack.Duration) - int(time.Now().Unix()),
				})
			}
			s.Status = "success"
			json.NewEncoder(w).Encode(s)
			return
		} else {
			w.WriteHeader(404)
			w.Write([]byte("404 page not found"))
		}
	}))
}
