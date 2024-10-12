package internal

import (
	"api/core/database"
	"api/core/master/sessions"
	"api/core/models"
	"api/core/models/functions"
	"html/template"
	"net/http"
	"time"

	"github.com/google/uuid"
)

func Login(w http.ResponseWriter, r *http.Request) {
	type Page struct {
		Name   string
		Title  string
		Script template.HTML
	}
	err := r.ParseForm()
	if err != nil {
		return
	}
	user, err := database.Container.GetUser(r.Form["login-username"][0])
	if err != nil {
		functions.Render(Page{
			Name:  models.Config.Name,
			Title: "Login",
			Script: template.HTML(functions.Toast(functions.Toastr{
				Icon:  "error",
				Title: "Error!",
				Text:  "Invalid credentials.",
			})),
		}, w, "login", "login.html")
		return
	}

	if user == nil {
		functions.Render(Page{
			Name:  models.Config.Name,
			Title: "Login",
			Script: template.HTML(functions.Toast(functions.Toastr{
				Icon:  "error",
				Title: "Error!",
				Text:  "Invalid credentials.",
			})),
		}, w, "login", "login.html")
		return
	}
	if !user.IsKey([]byte(r.Form["login-password"][0])) {
		functions.Render(Page{
			Name:  models.Config.Name,
			Title: "Login",
			Script: template.HTML(functions.Toast(functions.Toastr{
				Icon:  "error",
				Title: "Error!",
				Text:  "Invalid credentials.",
			})),
		}, w, "login", "login.html")
		return
	}
	sessionToken := uuid.NewString()
	expiresAt := time.Now().Add(30 * time.Minute)
	if _, remember := r.Form["remember-me"]; remember {
		expiresAt = time.Now().Add(48 * time.Hour)
	}

	sessions.Sessions[sessionToken] = sessions.Session{
		User:   user,
		Expiry: expiresAt,
	}
	http.SetCookie(w, &http.Cookie{
		Name:    "session-token",
		Value:   sessionToken,
		Expires: expiresAt,
	})

	http.Redirect(w, r, "/dashboard", http.StatusTemporaryRedirect)
}
