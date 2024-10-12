package adminapi

import (
    "api/core/database"
	"api/core/master/sessions"
    "encoding/json"
    "log"
    "api/core/models/server"
    "net/http"
    "strings"
)

func init() {
    Route.NewSub(server.NewRoute("/news", func(w http.ResponseWriter, r *http.Request) {
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
            var newNews database.News
            if err := json.NewDecoder(r.Body).Decode(&newNews); err != nil {
                log.Println("Error decoding JSON request:", err)
                http.Error(w, err.Error(), http.StatusBadRequest)
                return
            }

            // Add the news to the database
            if err := database.Container.NewNews(&newNews); err != nil {
                log.Println("Error adding news to the database:", err)
                http.Error(w, "Failed to add news", http.StatusInternalServerError)
                return
            }

            // Respond with success status
            w.WriteHeader(http.StatusOK)
            w.Write([]byte("News added successfully"))
        } else {
            w.WriteHeader(http.StatusNotFound)
            w.Write([]byte("404 page not found"))
        }
    }))
}