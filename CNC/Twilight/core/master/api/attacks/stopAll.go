package attackapi

import (
	"api/core/models/functions"
	"api/core/models/server"
	"encoding/json"
	"net/http"
)

func init() {
	Route.NewSub(server.NewRoute("/stop_all", func(w http.ResponseWriter, r *http.Request) {
		_, ok := functions.GetKey(w, r)
		if !ok {
			return
		}
		json.NewEncoder(w).Encode(map[string]any{"error": false, "message": "succesfully stopped all running attacks!"})
	}))
}
