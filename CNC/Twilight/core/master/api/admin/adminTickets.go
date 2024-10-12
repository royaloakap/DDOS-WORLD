package adminapi

import (
    "api/core/database"
    "api/core/models/server"
    "encoding/json"
	"api/core/master/sessions"
    "net/http"
    "strings"
)

func init() {
    Route.NewSub(server.NewRoute("/allTickets", func(w http.ResponseWriter, r *http.Request) {
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

        type TicketResponse struct {
            ID       int64  `json:"id"`
            Title    string `json:"title"`
            Message  string `json:"message"`
            Status   string `json:"status"`
            Date     int64  `json:"date"`
            Username string `json:"username"`
        }
        type Status struct {
            Status  string          `json:"status"`
            Message string          `json:"message"`
            Tickets []*TicketResponse `json:"tickets"`
        }
            tickets, err := database.Container.GetAllTickets()
            if err != nil {
                json.NewEncoder(w).Encode(&Status{Status: "error", Message: err.Error(), Tickets: []*TicketResponse{}})
                return
            }
            
            var ticketList []*TicketResponse
            for _, ticket := range tickets {
                username, err := database.Container.GetUserByID(ticket.UserID)
                if err != nil {
                    json.NewEncoder(w).Encode(&Status{Status: "error", Message: err.Error(), Tickets: []*TicketResponse{}})
                    return
                }
                // Append the ticket with username to the list
                ticketList = append(ticketList, &TicketResponse{
                    ID:       ticket.ID,
                    Title:    ticket.Title,
                    Status:   ticket.Status,
                    Date:     ticket.Date,
                    Username: username.Username, // Assign the username
                })
            }
            json.NewEncoder(w).Encode(&Status{Status: "success", Message: "Ticket information", Tickets: ticketList})
        }
    }))
}