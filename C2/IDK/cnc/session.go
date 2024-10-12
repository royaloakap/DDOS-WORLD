package cnc

import (
	"time"

	"golang.org/x/crypto/ssh"
)

type Session struct {
	conn    *ssh.ServerConn
	user    *User
	created time.Time
}

type SessionInfo struct {
	User   string
	Uptime time.Time
}

var (
	Sessions map[int64]*Session = make(map[int64]*Session)
)

func Online() int {
	return len(Sessions)
}

func OnlineUsers() map[int]*SessionInfo {
	var online map[int]*SessionInfo = make(map[int]*SessionInfo)
	for i, session := range Sessions {
		online[int(i)] = &SessionInfo{
			User:   session.user.Username,
			Uptime: session.created,
		}
	}
	return online
}

func New(info *Session) {
	Sessions[int64(len(Sessions)+1)] = info
}
