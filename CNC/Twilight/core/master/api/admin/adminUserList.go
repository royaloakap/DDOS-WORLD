package adminapi

import (
	"api/core/database"
	"api/core/master/sessions"
	"api/core/models/server"
	"encoding/json"
	"net/http"
	"strings"
	"time"
)

func init() {
    Route.NewSub(server.NewRoute("/user-list", func(w http.ResponseWriter, r *http.Request) {
        if strings.ToLower(r.Method) == "post" {
            ok, user := sessions.IsLoggedIn(w, r)
            if !ok {
                http.Redirect(w, r, "/login", http.StatusTemporaryRedirect)
                return
            }
            if !user.HasPermission("admin") {
                http.Redirect(w, r, "/dashboard", http.StatusTemporaryRedirect)
                return
            }
            type User struct {
                ID          int    `json:"id"`
                Username    string `json:"username"`
                Concurrents int    `json:"conns"`
                Servers     int    `json:"servers"`
                Duration    int    `json:"duration"`
                Permissions string `json:"permissions"`
                Expiry      int64  `json:"expiry"`
            }
            type Status struct {
                Status string  `json:"status"`
                Users  []*User `json:"users"`
            }
            users, err := database.Container.GetUsers()
            if err != nil {
                http.Error(w, err.Error(), http.StatusInternalServerError)
                return
            }

            s := &Status{
                Status: "success",
                Users:  make([]*User, 0),
            }
            for _, user := range users {
                s.Users = append(s.Users, &User{
                    ID:          user.ID,
                    Username:    user.Username,
                    Concurrents: user.Concurrents,
                    Servers:     user.Servers,
                    Duration:    user.Duration,
                    Permissions: func() string {
                        if user.HasPermission("admin") {
                            return "admin"
                        } else if user.HasPermission("vip") {
                            return "VIP"
                        } else if user.HasPermission("cnc") {
                            return "CNC"
                        } else if user.HasPermission("api") {
                            return "API"
                        } else if user.Expiry > time.Now().Unix() {
                            return "member"
                        }
                        return "member"
                    }(),
                    Expiry: user.Expiry,
                })
            }
            w.Header().Set("Content-Type", "application/json")
            w.WriteHeader(http.StatusOK) // Set status code before writing any response body
            json.NewEncoder(w).Encode(s)
            return
        } else {
            w.WriteHeader(http.StatusNotFound)
            w.Write([]byte("404 page not found"))
            return
        }
    }))
}
