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
    Route.NewSub(server.NewRoute("/blacklist", func(w http.ResponseWriter, r *http.Request) {
        switch strings.ToLower(r.Method) {
        case "post":
            // Handle POST request to add a host to the blacklist
            handlePostRequest(w, r)
        case "get":
            // Handle GET request to retrieve all blacklisted hosts
            handleGetRequest(w, r)
        default:
            // Handle unsupported HTTP methods
            w.WriteHeader(http.StatusMethodNotAllowed)
            w.Write([]byte("Method not allowed"))
        }
    }))
}

// handlePostRequest handles the POST request to add a host to the blacklist
func handlePostRequest(w http.ResponseWriter, r *http.Request) {
    ok, session := sessions.IsLoggedIn(w, r)
    if !ok {
        http.Redirect(w, r, "/login", http.StatusTemporaryRedirect)
        return
    }
    if !session.HasPermission("admin") {
        http.Redirect(w, r, "/dashboard", http.StatusTemporaryRedirect)
        return
    }

    var request struct {
        Host string `json:"host"`
    }
    if err := json.NewDecoder(r.Body).Decode(&request); err != nil {
        log.Println("Error decoding JSON request:", err)
        http.Error(w, err.Error(), http.StatusBadRequest)
        return
    }

    // Add the host to the blacklist in the database
    if err := database.Container.NewBlacklist(request.Host); err != nil {
        log.Println("Error adding host to the blacklist:", err)
        http.Error(w, "Failed to add host to the blacklist", http.StatusInternalServerError)
        return
    }

    // Respond with success status
    w.WriteHeader(http.StatusOK)
    w.Write([]byte("Host added to the blacklist successfully"))
}

// handleGetRequest handles the GET request to retrieve all blacklisted hosts
func handleGetRequest(w http.ResponseWriter, r *http.Request) {
    // Fetch all blacklisted hosts from the database
    blacklists, err := database.Container.GetAllBlacklists()
    if err != nil {
        log.Println("Error retrieving blacklists from the database:", err)
        http.Error(w, "Failed to retrieve blacklists", http.StatusInternalServerError)
        return
    }

    // Marshal the blacklists into JSON
    blacklistJSON, err := json.Marshal(blacklists)
    if err != nil {
        log.Println("Error marshaling blacklists into JSON:", err)
        http.Error(w, "Failed to marshal blacklists into JSON", http.StatusInternalServerError)
        return
    }

    // Respond with the list of blacklisted hosts
    w.Header().Set("Content-Type", "application/json")
    w.WriteHeader(http.StatusOK)
    w.Write(blacklistJSON)
}
