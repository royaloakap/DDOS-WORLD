package servers

import (
	"bytes"
	"fmt"
	"log"
	"net"
	"slices"
	"strconv"
	"strings"
)

func Listen() {
	listener, err := net.Listen("tcp", fmt.Sprintf("0.0.0.0:%d", Config.Listener))
	if err != nil {
		log.Fatal(err)
	}
	logger.Println("listening for incoming connections on \"" + fmt.Sprint(Config.Listener) + "\"")
	for {
		conn, err := listener.Accept()
		if err != nil {
			log.Println(err)
			continue
		}
		server := New(conn)
		go server.Handle()
	}
}

func (s *Server) Handle() {
	if !slices.Contains(Config.Allowed, s.RemoteAddr()) {
		logger.Println("" + s.RemoteAddr() + " unathorized connection!")
		return
	}
	buf := make([]byte, len(Config.Key))
	n, err := s.Read(buf)
	if err != nil {
		log.Println(err)
		return
	}
	if n != len(Config.Key) || buf == nil {
		return
	}
	if !bytes.Equal(buf, []byte(Config.Key)) {
		logger.Println("" + s.RemoteAddr() + " failed to authenticate!")
		return
	}
	message, err := s.ReadMessage()
	if err != nil {
		logger.Println("" + s.RemoteAddr() + " error while reading message!")
		return
	}
	if message.ID != MessageAuthenticate {
		logger.Println("" + s.RemoteAddr() + " message ID mismatch!")
		return
	}
	data := strings.Split(string(message.Content), "|")
	name := data[0]
	slots, _ := strconv.Atoi(data[1])
	stype, _ := strconv.Atoi(data[2])
	s.Slots = slots
	s.Type = stype
	s.Name = name
	Servers[s.Name] = s
	logger.Println("" + s.RemoteAddr() + " registered as \"" + name + "\" with \"" + data[1] + "\" slots")
	s.NewMessage(MessageSuccess, "authenticated!")
	go s.Ongoing()
	s.KeepAlive()

}
