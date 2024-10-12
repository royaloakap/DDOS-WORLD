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
    Route.NewSub(server.NewRoute("/updateTicket", func(w http.ResponseWriter, r *http.Request) {
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

            // Decode JSON request body into a struct containing ticketID and status
            var request struct {
                TicketID int64  `json:"ticketid"`
                Status   string `json:"status"`
            }
            if err := json.NewDecoder(r.Body).Decode(&request); err != nil {
                log.Println("Error decoding JSON request:", err)
                http.Error(w, err.Error(), http.StatusBadRequest)
                return
            }

            // Update the ticket status in the database
            if err := database.Container.UpdateTicket(request.TicketID, request.Status); err != nil {
                log.Println("Error updating ticket status in the database:", err)
                http.Error(w, "Failed to update ticket status", http.StatusInternalServerError)
                return
            }

            // Respond with success status
            w.WriteHeader(http.StatusOK)
            w.Write([]byte("Ticket status updated successfully"))
        } else {
            w.WriteHeader(http.StatusNotFound)
            w.Write([]byte("404 page not found"))
        }
    }))
}