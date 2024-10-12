package server2

import (
    "net"
    "bufio"
    "fmt"
    "io"
    "strings"
    "api/core/database"
    "golang.org/x/crypto/ssh"
    "api/core/models"
    "api/core/net/commands"
    "api/core/net/sessions"
)

func HandleSSHConnection(conn net.Conn) {
    defer conn.Close()

    sshConn, chans, reqs, err := ssh.NewServerConn(conn, config)
    if err != nil {
        logger.Println("Error establishing SSH connection:", err)
        return
    }
    defer sshConn.Close()

    go ssh.DiscardRequests(reqs)

    for newChannel := range chans {
        // Accept only session channels
        if newChannel.ChannelType() != "session" {
            newChannel.Reject(ssh.UnknownChannelType, "unknown channel type")
            continue
        }
        channel, requests, err := newChannel.Accept()
        if err != nil {
            logger.Println("Error accepting channel:", err)
            return
        }

        go func(channel ssh.Channel, requests <-chan *ssh.Request) {
            defer channel.Close()

            // Retrieve the username from the SSH connection metadata
            username := sshConn.User()
            if username == "" || username == "unknown" {
                logger.Println("Username not provided by the SSH client.")
                return
            }

            // Retrieve user information from the database
            user, err := database.Container.GetUser(username)
            if err != nil {
                logger.Println("Failed to retrieve user information:", err)
                return
            }

            // Create a new session
            session := &sessions.Session{
                User: user,
                Conn: conn,
            }

            // Send the command prompt
            prompt := fmt.Sprintf("%s@%s# ", session.User.Username, models.Config.Name)
            fmt.Fprint(channel, prompt)


            reader := bufio.NewReader(channel)

            for {
                line, err := reader.ReadString('\n')
                if err != nil {
                    if err != io.EOF {
                        logger.Println("Error reading input:", err)
                    }
                    return
                }

                line = strings.TrimSpace(line)

                handleCommand(channel, line, session)

                fmt.Fprint(channel, prompt)
            }
        }(channel, requests)
    }
}

func handleCommand(channel ssh.Channel, cmd string, session *sessions.Session) {
    args := strings.Fields(cmd)
    if len(args) == 0 {
        channel.Write([]byte("Invalid command\n"))
        return
    }

    commandName := args[0]
    commandArgs := args[1:]

    if command, ok := commands.Commands[commandName]; ok {
        if command.Admin && !isAdmin(session) {
            channel.Write([]byte("Permission denied\n"))
            return
        }
        command.Exec(session, commandArgs)
    } else {
        channel.Write([]byte("Unknown command\n"))
    }
}

func isAdmin(session *sessions.Session) bool {
    if session.User.HasPermission("admin") {
        return true
    }
    return false
}
