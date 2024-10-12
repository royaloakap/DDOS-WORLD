package main

import (
	"encoding/base64"
	"encoding/json"
	"fmt"
	"net"
)

type Message struct {
	ID        int    `json:"mmid"`
	MessageID int    `json:"messageid"`
	Length    int    `json:"length"`
	Content   []byte `json:"mcontent"`
}

type AttackMessage struct {
	ID        int `json:"mmid"`
	MessageID int `json:"messageid"`
	Data      struct {
		User     int    `json:"user"`
		Target   string `json:"target"`
		Port     string `json:"port"`
		Method   string `json:"method"`
		Duration string `json:"duration"`
	} `json:"data"`
	Options struct {
		Threads string `json:"threads"`
		PPS     string `json:"pps"`
		Subnet  string `json:"subnet"`
	} `json:"options"`
}

func NewMessage(ID int, content string) (*Message, []byte) {
	CurrentID++
	m := &Message{
		ID:        ID,
		MessageID: CurrentID,
		Length:    len(content),
		Content:   []byte(content),
	}
	bytes, err := json.Marshal(m)
	if err != nil {
		logger.Println("error while encoding message!")
	}
	msg := base64.RawStdEncoding.EncodeToString(bytes)
	return m, []byte(msg)
}

func ReadMessage(conn net.Conn) (*Message, error) {
	length := make([]byte, 1)
	n, err := conn.Read(length)
	if err != nil || n != 1 {
		return nil, err
	}
	logger.Println("New message incoming (length=" + fmt.Sprint(length[0]) + ")")
	buf := make([]byte, length[0])
	n, err = conn.Read(buf)
	if err != nil || n < 0 {
		return nil, err
	}
	m := new(Message)
	decoded, err := base64.RawStdEncoding.DecodeString(string(buf[:n]))
	if err != nil {
		logger.Println("failed to decode buffer")
		return nil, err
	}
	err = json.Unmarshal(decoded, &m)
	//fmt.Println(string(decoded))
	if err != nil {
		logger.Println(err)
	}
	CurrentID++
	return m, nil
}

func ReadAttack(conn net.Conn) (*AttackMessage, error) {
	length := make([]byte, 1)
	n, err := conn.Read(length)
	if err != nil || n != 1 {
		return nil, err
	}
	buf := make([]byte, length[0])
	n, err = conn.Read(buf)
	if err != nil || n != int(length[0]) {
		return nil, err
	}
	m := new(AttackMessage)
	decoded, err := base64.RawStdEncoding.DecodeString(string(buf[:n]))
	if err != nil {
		logger.Println(err)
		return nil, err
	}
	fmt.Println(string(decoded))
	//fmt.Println(string(decoded))
	err = json.Unmarshal(decoded, &m)
	if err != nil {

		logger.Println(err)
		return nil, err
	}
	CurrentID++
	return m, nil
}
