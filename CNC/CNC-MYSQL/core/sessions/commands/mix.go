package mix_commands

import (
	"triton-cnc/core/sessions/commands/extra"
	"triton-cnc/core/sessions/commands/sessions"
	"triton-cnc/core/sessions/commands/users"
	"triton-cnc/core/sessions/commands/util"
	"triton-cnc/core/sessions/sessions"
	"triton-cnc/core/models/client/terminal"
	"triton-cnc/core/models/external"
)


type CommandType struct {
	Name string

	Description string

	Admin    bool
	Reseller bool
	Vip      bool

	Execute func(Session *sessions.Session_Store, cmd []string) error
}

func Mixer(name string) *CommandType {


	Commandv0 := external.Command[name]
	if Commandv0 != nil {
		var New = CommandType {
			Name: Commandv0.Name,
			Description: Commandv0.Description,
			Admin: Commandv0.Admin,
			Reseller: Commandv0.Reseller,
			Vip: Commandv0.VIP,
			Execute: func(Session *sessions.Session_Store, cmd []string) error {
				error, _ := terminal.BannerString(Commandv0.Banner, Session.User, Session.Channel, true, false, nil)
				return error
			},
		}
		return &New
	}


	Commandv1 := util_Command.Get(name)
	if Commandv1 != nil {
		var New = CommandType {
			Name: Commandv1.Name,
			Description: Commandv1.Description,
			Admin: Commandv1.Admin,
			Reseller: Commandv1.Reseller,
			Vip: Commandv1.Vip,
			Execute: Commandv1.Execute,
		}
		return &New
	}


	Commandv2 := extra_Command.Get(name)
	if Commandv2 != nil {
		var New = CommandType {
			Name: Commandv2.Name,
			Description: Commandv2.Description,
			Admin: Commandv2.Admin,
			Reseller: Commandv2.Reseller,
			Vip: Commandv2.Vip,
			Execute: Commandv2.Execute,
		}
		return &New
	}

	Commandv3 := user_Command.Get(name)
	if Commandv3 != nil {
		var New = CommandType {
			Name: Commandv3.Name,
			Description: Commandv3.Description,
			Admin: Commandv3.Admin,
			Reseller: Commandv3.Reseller,
			Vip: Commandv3.Vip,
			Execute: Commandv3.Execute,
		}
		return &New
	}

	Commandv4 := sessions_Command.Get(name)
	if Commandv4 != nil {
		var New = CommandType {
			Name: Commandv4.Name,
			Description: Commandv4.Description,
			Admin: Commandv4.Admin,
			Reseller: Commandv4.Reseller,
			Vip: Commandv4.Vip,
			Execute: Commandv4.Execute,
		}
		return &New
	}


	return nil
}