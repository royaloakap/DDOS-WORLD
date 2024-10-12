package users_edit

import (
	"triton-cnc/core/sessions/sessions"
	"strings"
)

func GetUser(user string) []*sessions.Session_Store {
	var User []*sessions.Session_Store

	for _, I := range sessions.SessionMap {
		if strings.ToLower(I.User.Username) == strings.ToLower(user) {
			User = append(User, I)
		}
	}

	return User
}