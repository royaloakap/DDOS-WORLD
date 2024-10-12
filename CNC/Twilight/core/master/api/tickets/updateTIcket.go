package ticketapi

import (
    "api/core/database"
	"api/core/models/server"
    "api/core/master/sessions"
    "encoding/json"
    "net/http"
    "strings"
)
func init() {
    Route.NewSub(server.NewRoute("/update", func(w http.ResponseWriter, r *http.Request) {
        type Status struct {
            Status  string `json:"status"`
            Message string `json:"message"`
        }
        switch strings.ToLower(r.Method) {
        case "post":
            ok, user := sessions.IsLoggedIn(w, r)
            if !ok {
                http.Redirect(w, r, "/login", http.StatusTemporaryRedirect)
                return
            }

            // Decode the request body to get the ticket information
            var updateticket Ticket
            err := json.NewDecoder(r.Body).Decode(&updateticket)
            if err != nil {
                json.NewEncoder(w).Encode(&Status{Status: "error", Message: "failed to decode request body"})
                return
            }

            // Validate the ticket information
            if updateticket.Message == "" {
                json.NewEncoder(w).Encode(&Status{Status: "error", Message: "message required"})
                return
            }

            // Save the ticket information in the database
            err = database.Container.UpdateMessage(updateticket.TicketID, user.ID, updateticket.Message)
            if err != nil {
                json.NewEncoder(w).Encode(&Status{Status: "error", Message: err.Error()})
                return
            }

            json.NewEncoder(w).Encode(&Status{Status: "success", Message: "ticket submitted successfully"})
        }
    }))
}