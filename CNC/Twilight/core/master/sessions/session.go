package sessions

import (
	"api/core/database"
	"log"
	"os"
	"time"
)

var (
	logger   = log.New(os.Stderr, "[sessions] ", log.Ltime|log.Lshortfile)
	Sessions = map[string]Session{}
)

// Session is used to store the user & expiry
type Session struct {
	*database.User
	Expiry time.Time
}

// IsExpired is used to determine if the Session has expired
func (s Session) IsExpired() bool {
	return s.Expiry.Before(time.Now())
}

func Count() int {
    return len(Sessions)
}
