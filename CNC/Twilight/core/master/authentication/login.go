package authentication

import (
	"api/core/master/internal"
	"api/core/master/sessions"
	"api/core/models"
	"api/core/models/functions"
	"api/core/models/server"
	"html/template"
	"net/http"
	"strings"
)

func init() {
	Route.NewSub(server.NewRoute("/login", func(w http.ResponseWriter, r *http.Request) {
		if ok, _ := sessions.IsLoggedIn(w, r); ok {
			http.Redirect(w, r, "/dashboard", http.StatusTemporaryRedirect)
		}
		switch strings.ToLower(r.Method) {
		case "get":
			type Page struct {
				Name   string
				Title  string
				Script template.JS
			}
			functions.Render(Page{
				Name:  models.Config.Name,
				Title: "Login",
			}, w, "login", "login.html")
		case "post":
			internal.Login(w, r)
		}
	}))
}
