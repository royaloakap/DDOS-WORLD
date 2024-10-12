package dashboardapi

import (
    "api/core/master/sessions"
    "api/core/models/server"
    "encoding/json"
    "net/http"
)

func init() {
    Route.NewSub(server.NewRoute("/user_id", func(w http.ResponseWriter, r *http.Request) {
        // Check if the user is logged in
        ok, user := sessions.IsLoggedIn(w, r)
        if !ok {
            http.Error(w, "User not logged in", http.StatusUnauthorized)
            return
        }

        // Respond with the user ID and membership in JSON format
        response := struct {
            UserID       int    `json:"user_id"`
            UserMembership string `json:"user_membership"`
        }{
            UserID:       user.ID,
            UserMembership: user.Membership,
        }
        w.Header().Set("Content-Type", "application/json")
        json.NewEncoder(w).Encode(response)
    }))
}
