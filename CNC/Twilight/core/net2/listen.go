package server2

import (
	"api/core/models"
	"net"
)

func Listen() error {
	listener, err := net.Listen("tcp4", "0.0.0.0:"+models.Config.Server.SSH)
	if err != nil {
		return err
	}
	defer listener.Close()

	for {
		conn, err := listener.Accept()
		if err != nil {
			logger.Println(err)
			continue
		}
		go HandleSSHConnection(conn)
	}
}
