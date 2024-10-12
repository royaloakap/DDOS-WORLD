package ticketapi

import (
	"api/core/database"
	"api/core/master/sessions"
	"api/core/models/server"
	"encoding/json"
	"log"
	"net/http"
	"strings"
)

// Ticket represents the structure of a ticket.
type Ticket struct {
    User     *database.User  `json:"username"`
    TicketID int64           `json:"ticketid"`
    Title    string          `json:"title"`
    Message  string          `json:"message"`
    Date     int64           `json:"date"`
}

func init() {
    Route.NewSub(server.NewRoute("/newTicket", func(w http.ResponseWriter, r *http.Request) {
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
            var newTicket Ticket
            err := json.NewDecoder(r.Body).Decode(&newTicket)
            if err != nil {
                json.NewEncoder(w).Encode(&Status{Status: "error", Message: "failed to decode request body"})
                return
            }

            // Validate the ticket information
            if newTicket.Title == "" || newTicket.Message == "" {
                json.NewEncoder(w).Encode(&Status{Status: "error", Message: "title and message are required"})
                return
            }
            log.Print(newTicket.Title, user.ID, newTicket.Message)
            // Save the ticket information in the database
            err = database.Container.NewTicket(user.ID, newTicket.Title, newTicket.Message)
            if err != nil {
                json.NewEncoder(w).Encode(&Status{Status: "error", Message: err.Error()})
                return
            }

            json.NewEncoder(w).Encode(&Status{Status: "success", Message: "ticket submitted successfully"})
        }
    }))
}