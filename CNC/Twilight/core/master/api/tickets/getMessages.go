package ticketapi

import (
	"api/core/database"
	"api/core/models/server"
	"api/core/master/sessions"
	"encoding/json"
	"net/http"
	"strconv"
	"strings"
	"time"
)

type Message struct {
	ID        int64     `json:"id"`
	TicketID  int64     `json:"ticketid"`
	UserID    int64     `json:"user_id"`
	Message   string    `json:"message"`
	CreatedAt time.Time `json:"created_at"`
}

func init() {
	Route.NewSub(server.NewRoute("/messages", func(w http.ResponseWriter, r *http.Request) {
		type MessageResponse struct {
			Status   string     `json:"status"`
			Messages []*Message `json:"messages"`
		}

		switch strings.ToLower(r.Method) {
		case "post":
			ok, _ := sessions.IsLoggedIn(w, r)
			if !ok {
				http.Redirect(w, r, "/login", http.StatusTemporaryRedirect)
				return
			}
			id := r.PostFormValue("ticketid")
			ticketID, _ := strconv.Atoi(id)
			if ticketID == 0 {
				http.Error(w, "Ticket ID is required", http.StatusBadRequest)
				return
			}

			// Retrieve messages for the given ticket ID
			messages, err := database.Container.GetMessagesByTicketID(int64(ticketID))
			if err != nil {
				http.Error(w, "Failed to fetch messages", http.StatusInternalServerError)
				return
			}

			// Convert database messages to API messages
			apiMessages := convertToAPIModel(messages)

			// Construct response
			response := MessageResponse{
				Status:   "success",
				Messages: apiMessages,
			}

			// Encode the response as JSON
			json.NewEncoder(w).Encode(response)
		}
	}))
}

func convertToAPIModel(messages []*database.Message) []*Message {
	var apiMessages []*Message
	for _, msg := range messages {
		apiMsg := Message{
			ID:        msg.ID,
			UserID:    msg.UserID,
			TicketID:  msg.TicketID,
			Message:   msg.Message,
			CreatedAt: msg.CreatedAt,
		}
		apiMessages = append(apiMessages, &apiMsg)
	}
	return apiMessages
}
