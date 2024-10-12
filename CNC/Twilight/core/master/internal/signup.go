package internal

import (
	"api/core/database"
	"api/core/master/sessions"
	"api/core/models"
	"api/core/models/antiflood"
	"api/core/models/ranks"
	"api/core/models/functions"
	"errors"
	"fmt"
	"html/template"
	"io"
	"math/rand"
	"net"
	"net/http"
	"strconv"
	"strings"
	"time"

	"github.com/google/uuid"
)
type Page struct {
	Name   string
	Title  string
	Script template.HTML
}

var (
	SignupLimiter = antiflood.NewRateLimiter(
		1,
		120*time.Minute,
		antiflood.WithKeyByRealIP(),
	)
	captchas map[string]string = make(map[string]string)
)

func GetBodyData(data string) string {
	fmt.Println(data)
	return strings.Split(data, "=")[1]
}

func NewCaptcha(r *http.Request, answer string) {
	ip := KeyByRealIP(r)
	captchas[ip] = answer
}

func generateCaptcha() (string, string) {
	rand.Seed(time.Now().UnixNano())

	// Generate two random numbers between 0 and 9
	num1 := rand.Intn(10)
	num2 := rand.Intn(10)
	var answer int

	operator := []string{"+", "-", "*"}[rand.Intn(3)]

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

func KeyByRealIP(r *http.Request) string {
	var ip string

	if tcip := r.Header.Get("True-Client-IP"); tcip != "" {
		ip = tcip
	} else if xrip := r.Header.Get("X-Real-IP"); xrip != "" {
		ip = xrip
	} else if xff := r.Header.Get("X-Forwarded-For"); xff != "" {
		i := strings.Index(xff, ", ")
		if i == -1 {
			i = len(xff)
		}
		ip = xff[:i]
	} else if ccip := r.Header.Get("CF-Connecting-IP"); ccip != "" {
		ip = ccip
	} else {
		var err error
		ip, _, err = net.SplitHostPort(r.RemoteAddr)
		if err != nil {
			ip = r.RemoteAddr
		}
	}

	return ip
}

func Signup(w http.ResponseWriter, r *http.Request) {

	ip := KeyByRealIP(r)
	body, _ := io.ReadAll(r.Body)
	data := strings.Split(string(body), "&")
	fmt.Println(data)

	if len(data) < 6 {
		renderErrorPage(w, r, "Please fill out all fields!")
		return
	}

	username := GetBodyData(data[0])
	password := GetBodyData(data[1])
	cpassword := GetBodyData(data[2])
	captcha := GetBodyData(data[4])
	tos := GetBodyData(data[5])

	// Validate form data
	if err := validateSignupData(username, password, cpassword, tos); err != nil {
		renderErrorPage(w, r, err.Error())
		return
	}

	// Check captcha
	if answer, ok := captchas[ip]; !ok || captcha != answer {
		renderErrorPage(w, r, "Invalid captcha provided.")
		return
	}
	delete(captchas, ip)

	// Check if user already exists
	user, err := database.Container.GetUser(username)
	if err != nil && !errors.Is(err, database.ErrUserNotFound) {
		renderDatabaseErrorPage(w, r, "Error retrieving user from database.")
		return
	}

	if user != nil {
		renderErrorPage(w, r, "User already exists.")
		return
	}

	// Create new user
	err = database.Container.NewUser(&database.User{
		Username:    username,
		Key:         []byte(password),
		Membership: "Free",
		Ranks: []*ranks.Rank{
			ranks.GetRole("member", true),
		},
		Concurrents: 1,
		Servers:     0,
		Duration:    120,
		Expiry:      -1,
	})
	if err != nil {
		renderDatabaseErrorPage(w, r, "Error creating new user in database.")
		return
	}

	// Set session and redirect to dashboard
	user, _ = database.Container.GetUser(username)
	sessionToken := uuid.NewString()
	expiresAt := time.Now().Add(30 * time.Minute)
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

func renderErrorPage(w http.ResponseWriter, r *http.Request, errorMessage string) {
	exp, ans := generateCaptcha()
	functions.Render(Page{
		Name:  models.Config.Name,
		Title: "Register",
		Script: template.HTML(functions.Toast(functions.Toastr{
			Icon:  "error",
			Title: "Error!",
			Text:  errorMessage,
		}) + `<script>
		$(window).on('load', function() {
			console.log('` + exp + `')
			const captcha = document.getElementById('signup-captcha');
			captcha.placeholder = '` + exp + `';
		});
		</script>`),
	}, w, "login", "signup.html")
	delete(captchas, KeyByRealIP(r))
	NewCaptcha(r, ans)
}

func renderDatabaseErrorPage(w http.ResponseWriter, r *http.Request, errorMessage string) {
	renderErrorPage(w, r, "Database error occurred: "+errorMessage)
}


func validateSignupData(username, password, cpassword, tos string) error {
	if len(username) < 4 {
		return errors.New("username must be at least 4 characters")
	}

	if len(password) < 8 {
		return errors.New("password must be at least 8 characters")
	}

	if password != cpassword {
		return errors.New("passwords do not match")
	}

	if tos != "on" {
		return errors.New("you must agree to the terms of service")
	}

	return nil
}