package ticketapi

import (
	"api/core/database"
	"api/core/master/sessions"
	"api/core/models/server"
	"encoding/json"
	"net/http"
	"strings"
)

func init() {
	Route.NewSub(server.NewRoute("/history", func(w http.ResponseWriter, r *http.Request) {
		type Ticket struct {
			ID      int64  `json:"id"`
			Title   string `json:"title"`
			Message string `json:"message"`
			Status  string `json:"status"`
			Date    int64  `json:"date"`
		}
		type Status struct {
			Status  string    `json:"status"`
			Message string    `json:"message"`
			Tickets []*Ticket `json:"tickets"`
		}
		switch strings.ToLower(r.Method) {
		case "post":
			ok, user := sessions.IsLoggedIn(w, r)
			if !ok {
				http.Redirect(w, r, "/login", http.StatusTemporaryRedirect)
				return
			}
			//db container to get tickets
			tickets, err := database.Container.GetTickets(user.User)
			if err != nil {
				json.NewEncoder(w).Encode(&Status{Status: "error", Message: err.Error(), Tickets: []*Ticket{}})
				return
			}
			//get tickets
			var ticketList []*Ticket
			//make a row for every ticket
			for _, ticket := range tickets {
				ticketList = append(ticketList, &Ticket{
					ID:      ticket.ID,
					Title:   ticket.Title,
					Status:  ticket.Status,
					Date:    ticket.Date,
				})
			}
			json.NewEncoder(w).Encode(&Status{Status: "success", Message: "ticket information", Tickets: ticketList})
		}
	}))
}