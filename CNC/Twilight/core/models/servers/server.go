package servers

import (
	"api/core/models/apis"
	"api/core/models/floods"
	"fmt"
	"log"
	"math"
	"net"
	"slices"
	"strings"
	"time"
)

type Server struct {
	Name      string
	Type      int
	Slots     int
	running   int
	CurrentID int
	attacks   map[int]*floods.Attack
	Queue     chan *floods.Attack
	StopQueue chan string
	conn      net.Conn
}

func (server *Server) Load() float64 {
	log.Println(server.Name, server.running, server.Slots, fmt.Sprintf("%.2f", (float64(server.running)/float64(server.Slots))*100))
	return toFixed(((float64(server.running) / float64(server.Slots)) * 100), 2)
}

func New(conn net.Conn) *Server {
	return &Server{
		attacks:   make(map[int]*floods.Attack, 0),
		conn:      conn,
		CurrentID: 0,
		Queue:     make(chan *floods.Attack, 30),
	}
}

func (s *Server) RemoteAddr() string {
	return strings.Split(s.conn.RemoteAddr().String(), ":")[0]
}

func (s *Server) Read(buf []byte) (int, error) {
	return s.conn.Read(buf)
}

func (s *Server) Running() int {
	return s.running
}

func SelectHandler(sType int) *Server {
	if len(Servers) == 0 {
		return nil
	}
	var load []int = make([]int, 0)
	for _, server := range Servers {
		if server.running == server.Slots {
			continue
		}
		if server.Type == sType {
			log.Println(server)
			load = append(load, server.running)
		}
	}
	min := slices.Min(load)
	for _, server := range Servers {
		if server.running == server.Slots {
			continue
		}
		if server.running == min && server.running < server.Slots {
			if server.Type != sType {
				continue
			}
			return server
		}
	}
	return nil
}

func (s *Server) KeepAlive() {
	ticker := time.NewTicker(10 * time.Second)
	for {
		select {
		case atk, ok := <-s.Queue:
			if !ok {
				continue
			}
			if (s.running + 1) == s.Slots {
				continue
			}
			logger.Println("starting attack on \"" + atk.Target + "\"")
			s.NewMessage(MessageAttack, "")
			s.running++
			s.attacks[len(s.attacks)] = atk
			s.NewAttack(atk)
		case stop, ok := <-s.StopQueue:
			if !ok {
				continue
			}
			s.NewMessage(MessageStop, stop)
			logger.Println("stopped attack \"" + stop + "\"")
		case <-ticker.C:
			s.NewMessage(MessagePing, "ping!")
			msg, err := s.ReadMessage()
			if err != nil {
				return
			}
			if msg.ID != MessagePing {
				log.Println("ping id mismatch!")
				return
			}
		default:
			time.Sleep(250 * time.Millisecond)
		}
	}
}

func Distribute(atk *floods.Attack) {
	fmt.Println("distributing attack across all servers!")
	handler := SelectHandler(atk.Mtype)
	if handler != nil {
		handler.Queue <- atk
	} else {
		for _, server := range Servers {
			if server.running < server.Slots && server.Type == atk.Mtype {
				server.Queue <- atk
			}
		}
	}
}

func Stop(id int, target string) {

}

func Slots() map[int]int {
	var i map[int]int = make(map[int]int)
	for _, server := range Servers {
		i[server.Type] += server.Slots
		i[0] += server.Slots
	}
	i[0] += apis.Slots()
	log.Println(i)
	return i
}

func (s *Server) Ongoing() {
	ticker := time.NewTicker(1 * time.Second)
	for {
		select {
		case <-ticker.C:
			for i, attack := range s.attacks {
				if attack.Created+int64(attack.Duration) == time.Now().Unix() {
					delete(s.attacks, i)
					s.running--
				}
			}
		}
	}
}

func round(num float64) int {
	return int(num + math.Copysign(0.5, num))
}

func toFixed(num float64, precision int) float64 {
	output := math.Pow(10, float64(precision))
	return float64(round(num*output)) / output
}
