package landing

import (
	"api/core/database"
	"api/core/master/sessions"
	"api/core/models"
	"api/core/models/apis"
	"api/core/models/functions"
	"api/core/models/server"
	"api/core/models/servers"
	"net/http"
)

func init() {
	Route.NewSub(server.NewRoute("/", func(w http.ResponseWriter, r *http.Request) {
		type Page struct {
			Name, Title, Vers string
			ServersCount      int
			Users             int
			*sessions.Session
		}
		functions.Render(Page{
			Name:         models.Config.Name,
			Title:        "Welcome!",
			Vers:         models.Config.Vers,
			ServersCount: len(servers.Servers) + len(apis.Apis),
			Users:        database.Container.Users(),
		}, w, "landing", "index.html")
	}))
}
