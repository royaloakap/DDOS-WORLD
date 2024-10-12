package ticketapi

import (
    "api/core/database"
    "api/core/master/sessions"
    "api/core/models/server"
    "encoding/json"
    "net/http"
    "strings"
    "strconv"
)

func init() {
    Route.NewSub(server.NewRoute("/ticket/{id}", func(w http.ResponseWriter, r *http.Request) {
        // Define response structure
        type Response struct {
            Status  string  `json:"status"`
            Message string  `json:"message,omitempty"`
            Ticket  *Ticket `json:"ticket,omitempty"`
        }

        parts := strings.Split(r.URL.Path, "/")
        ticketIDStr := parts[len(parts)-1]
        ticketID, err := strconv.ParseInt(ticketIDStr, 10, 64)
        if err != nil {
            // Respond with error if ticket ID is invalid
            json.NewEncoder(w).Encode(&Response{Status: "error", Message: "Invalid ticket ID"})
            return
        }

        switch strings.ToLower(r.Method) {
        case "get":
            // Check if user is logged in
            ok, _ := sessions.IsLoggedIn(w, r)
            if !ok {
                http.Redirect(w, r, "/login", http.StatusTemporaryRedirect)
                return
            }

            // Retrieve ticket information from the database
            ticket, err := database.Container.GetTicketByID(ticketID)
            if err != nil {
                // Respond with error if ticket retrieval fails
                json.NewEncoder(w).Encode(&Response{Status: "error", Message: err.Error()})
                return
            }

            // Respond with ticket information
            json.NewEncoder(w).Encode(&Response{
                Status: "success",
                Message: "Ticket information retrieved successfully",
                Ticket: &Ticket{
                    Title:    ticket.Title,
                    Message:  ticket.Message,
                    Date:     ticket.Date,
                },
            })
        }
    }))
}