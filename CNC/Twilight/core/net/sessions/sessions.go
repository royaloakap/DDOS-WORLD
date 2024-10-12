package sessions

import (
    "api/core/database"
    websessions "api/core/master/sessions"
    "api/core/net/term"
    "log"
    "net"
    "net/http"
    "sync"
    "time"

    "github.com/google/uuid"
)

var Sessions = make(map[int64]*Session)
var SessionMutex sync.Mutex

type Session struct {
    ID     int64
    User   *database.User
    cookie *http.Cookie
    Conn   net.Conn
    Term   *term.Term
    Chat   bool
}

func IsLoggedIn(userID int64) (*Session, bool) {
    SessionMutex.Lock()
    defer SessionMutex.Unlock()

    log.Println("Checking session for user ID:", userID)
    session, exists := Sessions[userID]
    if exists {
        log.Println("Session found for user ID:", userID)
        return session, true
    } else {
        log.Println("Session not found for user ID:", userID)
        return nil, false
    }
}

func Count() int {
    return len(Sessions)
}

func (s *Session) CreateCookie() {
    sessionToken := uuid.NewString()
    expiresAt := time.Now().Add(30 * time.Minute)
    websessions.Sessions[sessionToken] = websessions.Session{
        User:   s.User,
        Expiry: expiresAt,
    }
    s.cookie = &http.Cookie{
        Name:    "session-token",
        Value:   sessionToken,
        Expires: expiresAt,
    }
}

func (s *Session) Cookie() *http.Cookie {
    return s.cookie
}
