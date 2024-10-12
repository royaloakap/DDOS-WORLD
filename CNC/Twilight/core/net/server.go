package net

import (
	"api/core/models"
	"api/core/database"
    "crypto/rand"
    "crypto/rsa"
	"fmt"
	"log"
	"errors"
	"net"
	"os"
	"golang.org/x/crypto/ssh"
)

var listenAddr string
var sshListenAddr string
var logger = log.New(os.Stderr, "[net] ", log.Ltime|log.Lshortfile)
var sshLogger = log.New(os.Stderr, "[net-ssh] ", log.Ltime|log.Lshortfile)

func Listener() {
	listenAddr = fmt.Sprintf("0.0.0.0:%s", models.Config.Server.Telnet)
	sshListenAddr = fmt.Sprintf("0.0.0.0:%s", models.Config.Server.SSH)

	// Set up TCP listener for both SSH and Telnet
	tcpListener, err := net.Listen("tcp4", listenAddr)
	if err != nil {
		log.Fatalf("Failed to listen on Telnet %s: %v", listenAddr, err)
	}
	defer tcpListener.Close()

	sshListener, err := net.Listen("tcp4", sshListenAddr)
	if err != nil {
		log.Fatalf("Failed to listen on SSH %s: %v", sshListenAddr, err)
	}
	defer sshListener.Close()

	logger.Printf("CNC Started! | Telnet: %s | SSH: %s", listenAddr, sshListenAddr)

    config := &ssh.ServerConfig{
        ServerVersion: "SSH-2.0-" + models.Config.Name + "@" + models.Config.Vers,
        PasswordCallback: func(c ssh.ConnMetadata, pass []byte) (*ssh.Permissions, error) {
            user, err := database.Container.GetUser(c.User())
            if err != nil {
                return nil, err
            }
            if !user.IsKey(pass) {
                return nil, errors.New("invalid password")
            }
            return nil, nil
        },
    }

    keyBytes, err := rsa.GenerateKey(rand.Reader, 2048)
    if err != nil {
        logger.Fatal(err)
    }
    key, err := ssh.NewSignerFromSigner(keyBytes)
    if err != nil {
        logger.Fatal(err)
    }
    config.AddHostKey(key)

	// Accept incoming connections and handle them
	for {
		select {
		case telnetConn := <-accept(tcpListener):
			logger.Printf("New Telnet connection from: %s", telnetConn.RemoteAddr())
			go handler(telnetConn)
		case sshConn := <-accept(sshListener):
			sshLogger.Printf("New SSH connection from: %s", sshConn.RemoteAddr())
			go SSHHandler(sshConn, config)
		}
	}
}

// accept accepts incoming connections on the listener
func accept(listener net.Listener) chan net.Conn {
	ch := make(chan net.Conn)
	go func() {
		for {
			conn, err := listener.Accept()
			if err != nil {
				logger.Printf("Failed to accept incoming connection: %v", err)
				continue
			}
			ch <- conn
		}
	}()
	return ch
}
