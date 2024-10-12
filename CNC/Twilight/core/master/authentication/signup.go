package authentication

import (
	"api/core/master/internal"
	"api/core/models"
	"api/core/models/functions"
	"api/core/models/server"
	"fmt"
	"html/template"
	"math/rand"
	"net/http"
	"strconv"
	"strings"
	"time"
)

func generateCaptcha() (string, string) {
	rand.Seed(time.Now().UnixNano())

	// Generate two random numbers between 0 and 9
	num1 := rand.Intn(10)
	num2 := rand.Intn(10)
	var answer int

	// Choose a random operator: +, -, *
	operator := []string{"+", "-", "*"}[rand.Intn(3)]

	// Create the expression
	expression := fmt.Sprintf("%d %s %d", num1, operator, num2)

	// Calculate the answer
	switch operator {
	case "+":
		answer = num1 + num2
	case "-":
		answer = num1 - num2
	case "*":
		answer = num1 * num2
	}

	return expression, strconv.Itoa(answer)
}

func init() {
	Route.NewSub(server.NewRoute("/signup", func(w http.ResponseWriter, r *http.Request) {
		switch strings.ToLower(r.Method) {
		case "get":
			type Page struct {
				Name   string
				Title  string
				Script template.HTML
			}
			exp, ans := generateCaptcha()
			internal.NewCaptcha(r, ans)
			functions.Render(Page{
				Name:  models.Config.Name,
				Title: "Signup",
				Script: template.HTML(`<script>
$(window).on('load', function() {
	console.log('` + exp + `')
	const captcha = document.getElementById('signup-captcha');
	captcha.placeholder = '` + exp + `';
});
</script>`),
			}, w, "login", "signup.html")
		case "post":
			internal.Signup(w, r)
			/*
				type status struct {
					Status  string `json:"status"`
					Message string `json:"message"`
				}
				user, err := database.Container.GetUser(r.PostFormValue("username"))
				if err != nil {
					json.NewEncoder(w).Encode(&status{Status: "error", Message: "database error occured!"})
					return
				}
				if user != nil {
					json.NewEncoder(w).Encode(&status{Status: "error", Message: "user already exists!"})
					return
				}
				err = database.Container.NewUser(&database.User{
					Username:    r.PostFormValue("username"),
					Key:         []byte(r.PostFormValue("password")),
					Concurrents: 0,
					Servers:     0,
					Duration:    0,
					Expiry:      -1,
					Balance:     0,
					Membership:  "free",
				})
				if err != nil {
					json.NewEncoder(w).Encode(&status{Status: "error", Message: "database error occured!" + err.Error()})
					return
				}
				sessionToken := uuid.NewString()
				expiresAt := time.Now().Add(30 * time.Minute)
				if _, remember := r.Form["remember-me"]; remember {
					expiresAt = time.Now().Add(24 * time.Hour)
				}
				user, err = database.Container.GetUser(r.PostFormValue("username"))
				if err != nil {
					json.NewEncoder(w).Encode(&status{Status: "error", Message: "database error occured!"})
					return
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
				json.NewEncoder(w).Encode(&status{Status: "success", Message: "You will be redirected to the dashboard in 5 seconds."})*/
		}
	}))
}
