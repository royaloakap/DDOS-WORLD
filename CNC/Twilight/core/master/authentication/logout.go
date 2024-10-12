package authentication

import (
	"api/core/master/sessions"
	"api/core/models/server"
	"net/http"
	"time"
)

func init() {
	Route.NewSub(server.NewRoute("/logout", func(w http.ResponseWriter, r *http.Request) {
		if ok, _ := sessions.IsLoggedIn(w, r); !ok {
			http.Redirect(w, r, "/login", http.StatusTemporaryRedirect)
		}
		c, err := r.Cookie("session-token")
		if err != nil {
			if err == http.ErrNoCookie {
				w.WriteHeader(http.StatusUnauthorized)
				return
			}
			w.WriteHeader(http.StatusBadRequest)
			return
		}

		delete(sessions.Sessions, c.Value)

		http.SetCookie(w, &http.Cookie{
			Name:    "session-token",
			Value:   "",
			Expires: time.Now(),
		})

		http.Redirect(w, r, "/login", http.StatusTemporaryRedirect)
	}))
}
