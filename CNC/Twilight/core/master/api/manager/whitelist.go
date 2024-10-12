package managerapi

import (
	"api/core/models/server"
	"net/http"
)

func init() {
	Route.NewSub(server.NewRoute("/whitelist", func(w http.ResponseWriter, r *http.Request) {}))
}
