package main

import (
	"log"
	"net"
)

// Master will start the main master process for admins connecting
func Master() {
	listener, err := net.Listen(Options.Templates.Server.Protocol, Options.Templates.Server.Listener)
	if err != nil {
		log.Fatalf("Err: %v\r\n", err)
	}

	log.Printf("\x1b[48;5;10m\x1b[38;5;16m Success \x1b[0m Master server has been started {%s}\r\n", Options.Templates.Server.Listener)
	go Title()
	OnStart()

	for {
		conn, err := listener.Accept()
		if err != nil {
			continue
		}

		go Admin(conn)
	}
}