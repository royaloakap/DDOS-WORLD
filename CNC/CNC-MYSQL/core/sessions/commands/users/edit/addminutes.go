package users_edit

import (
	"strconv"
	"strings"
	"time"
	"triton-cnc/core/mysql"
	"triton-cnc/core/sessions/sessions"
	"triton-cnc/core/models/json/build"
)


func AddMinutes(session *sessions.Session_Store, cmd []string, stringsep []string) {

	if !strings.Contains(strings.Replace(strings.Join(stringsep, "="), stringsep[1], "", -1), "add_minutes=") || len(cmd) <= 2 {
		session.Channel.Write([]byte("\x1b[0m"+build.Config.AppConfig.AppName+" -> Command Example: users add_minutes=<Minutes> [users array, eg \"root root123456\"]\r\n"))
		return
	}

	Minutes, error := strconv.Atoi(stringsep[1])
	if error != nil {
		session.Channel.Write([]byte("\x1b[0m"+build.Config.AppConfig.AppName+" -> Minutes \""+stringsep[1]+"\" must be an int\r\n"))
		return
	}

	for LenCon := 2; LenCon < len(cmd); LenCon++ {
		User, error := database.GetUser(cmd[LenCon])
		if error != nil || User == nil {
			session.Channel.Write([]byte("\x1b[0m"+build.Config.AppConfig.AppName+" -> \""+cmd[LenCon]+"\" wasnt found in database!\r\n"))
			continue
		}

		boolen_error := database.AddTime(cmd[LenCon], time.Duration(Minutes) * time.Minute)
		if !boolen_error {
			session.Channel.Write([]byte("\x1b[0m"+build.Config.AppConfig.AppName+" -> failed to add Minutes to \""+cmd[LenCon]+"\" plan"))
			continue
		} else {
			session.Channel.Write([]byte("\x1b[0m\"\x1b[38;5;87m"+cmd[LenCon]+"\x1b[0m\" has now got \""+stringsep[1]+" Minutes longer\"\r\n"))
		}


		for _, Session := range sessions.SessionMap {
			if Session.User.Username == cmd[LenCon] {
				Session.Channel.Write([]byte("\x1b[0m\x1b7\x1b[1A\r\x1b[2K \x1b[38;5;209m"+stringsep[1]+" Minutes have been added to your plan!\x1b[0m\x1b[38;5;15m\x1b8"))

				End := time.Unix(session.User.PlanExpiry, 0)

				NewEnd := End.Add(time.Duration(Minutes) * time.Minute).Unix()
				Session.User.PlanExpiry = NewEnd
			}
		}

	}


}