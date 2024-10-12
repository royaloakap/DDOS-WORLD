package dashboardapi

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
	Route.NewSub(server.NewRoute("/running-attacks", func(w http.ResponseWriter, r *http.Request) {
		if strings.ToLower(r.Method) == "post" {
			ok, _ := sessions.IsLoggedIn(w, r)
			if !ok {
				http.Redirect(w, r, "/login", http.StatusTemporaryRedirect)
				return
			}
			type attacks struct {
				Layer4 []int `json:"Layer4"`
				Layer7 []int `json:"Layer7"`
			}
			times := map[int]int{
				int(time.Now().Unix()) - 300:  int(time.Now().Unix()),
				int(time.Now().Unix()) - 600:  int(time.Now().Unix()) - 301,
				int(time.Now().Unix()) - 900:  int(time.Now().Unix()) - 601,
				int(time.Now().Unix()) - 1200: int(time.Now().Unix()) - 901,
				int(time.Now().Unix()) - 1500: int(time.Now().Unix()) - 1201,
				int(time.Now().Unix()) - 1800: int(time.Now().Unix()) - 1501,
			}
			var a attacks
			for start, end := range times {
				attacks := database.Container.GetFromTo(start, end, 1)
				a.Layer4 = append(a.Layer4, attacks)
			}
			for start, end := range times {
				attacks := database.Container.GetFromTo(start, end, 2)
				a.Layer7 = append(a.Layer7, attacks)
			}
			json.NewEncoder(w).Encode(a)
		} else {
			w.Write([]byte("404 page not found"))
			w.WriteHeader(404)
		}
	}))
}
