package net

import (
    "api/core/database"
    "api/core/models"
    "api/core/net/sessions"
    "fmt"
    "net"

    "golang.org/x/crypto/ssh"
)


func SSHHandler(sshConn net.Conn, config *ssh.ServerConfig) {
    defer sshConn.Close()

    // Perform SSH handshake to establish an SSH connection
    sshServerConn, chans, reqs, err := ssh.NewServerConn(sshConn, config)
    if err != nil {
        fmt.Println("Failed to establish SSH connection:", err)
        return
    }
    defer sshServerConn.Close()

    // Perform SSH authentication
    username := sshServerConn.User()
    user, err := database.Container.GetUser(username)
    if err != nil {
        fmt.Println("Database error:", err)
        return
    }
	var session = &sessions.Session{
		User: user,
        Conn: sshConn,
	}

    session.CreateCookie()

    // Add session to sessions map
    sessions.SessionMutex.Lock()
    sessions.Sessions[session.ID] = session
    sessions.SessionMutex.Unlock()

    // Start session handling loop
    go sessionHandler(session, sshConn)

    // Handle SSH requests (e.g., keep-alives)
    go ssh.DiscardRequests(reqs)

    // Accept SSH channels
    for newChannel := range chans {
        // Accept only session channels
        if newChannel.ChannelType() != "session" {
            newChannel.Reject(ssh.UnknownChannelType, "unknown channel type")
            continue
        }

    }
}

func sessionHandler(session *sessions.Session, sshConn net.Conn,) {
    defer func() {
        // Remove session when it's closed
        RemoveSession(session.ID)
    }()
    // Send initial message
    sshConn.Write([]byte(fmt.Sprintf("\033]0;%s - Login\007", models.Config.Name)))
}