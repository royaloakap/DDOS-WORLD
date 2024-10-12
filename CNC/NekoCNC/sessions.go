package main

import (
	"sync"
	"net"
	"time"
)

var (
	sessions     = make(map[int64]*Session)
	sessionMutex sync.Mutex
)

//Session holds information on a open admin connection
type Session struct {
	ID int64
	Userconn net.Conn
	Username string
	Created  int64
}

//Remove will delete the session from the current store
func (session *Session) Remove() {
	sessionMutex.Lock()
	delete(sessions, session.ID)
	sessionMutex.Unlock()
}

func addSession(username string, myconnection net.Conn) *Session {
	sessionMutex.Lock()
	defer sessionMutex.Unlock()

	for {
		id := time.Now().UnixNano()

		_, ok := sessions[id]
		if ok == true {
			//Session ID taken, wait 10 nano seconds
			time.Sleep(time.Nanosecond * 10)
			continue
		}

		sessions[id] = &Session{
			ID:       id,
			Username: username,
			Created:  time.Now().Unix(),
			Userconn: myconnection,

		}

		return sessions[id]
	}
}

func onlineUsers() int {
	sessionMutex.Lock()
	defer sessionMutex.Unlock()

	return len(sessions)
}

func usersSessions(username string) []*Session {
	sessionMutex.Lock()
	defer sessionMutex.Unlock()

	var list []*Session
	for i := range sessions {
		if sessions[i].Username == username {
			list = append(list, sessions[i])
		}
	}

	return list
}
