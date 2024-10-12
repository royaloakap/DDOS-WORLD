package list_users

import (
	"fmt"

	"triton-cnc/core/models/json/build"
	"triton-cnc/core/models/util"
	"triton-cnc/core/mysql"
	"triton-cnc/core/sessions/sessions"
	"strconv"
	"strings"

	"github.com/alexeyco/simpletable"
)

func ListUser(session *sessions.Session_Store) error {


	table := simpletable.New()

	table.Header = &simpletable.Header{
		Cells: []*simpletable.Cell{
			{Align: simpletable.AlignCenter, Text: "\x1b[38;5;11m#\x1b[38;5;15m"},
			{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15mUser\x1b[38;5;15m"},
			{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15mAdmin\x1b[38;5;15m"},
			{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15mReseller\x1b[38;5;15m"},
			{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15mVip\x1b[38;5;15m"},
			{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15mBanned\x1b[38;5;15m"},
			{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15mPlan\x1b"+build.Config.AppConfig.AppColour+"-\x1b[38;5;15mActive\x1b[38;5;15m"},
		},
	}

	Users, error := database.GetUsers()
	if error != nil {
		return error
	}
	
	
	for _, User := range Users {
		r := []*simpletable.Cell{
			{Align: simpletable.AlignCenter, Text: "\x1b[38;5;11m"+strconv.Itoa(User.ID)+"\x1b[38;5;15m"},
			{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15m"+User.Username+"\x1b[38;5;15m"},
			{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15m"+util.Colour(User.Administrator, true)+"\x1b[38;5;15m"},
			{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15m"+util.Colour(User.Reseller, true)+"\x1b[38;5;15m"},
			{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15m"+util.Colour(User.Vip, true)+"\x1b[38;5;15m"},
			{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15m"+util.Colour(User.Banned, true)+"\x1b[38;5;15m"},
			{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15m"+util.Colour(util.Plan_expiry(User.PlanExpiry), true)+"\x1b[38;5;15m"},
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
		
	fmt.Fprint(session.Channel, "")
	fmt.Fprintln(session.Channel, strings.ReplaceAll(table.String(), "\n", "\r\n"))
	fmt.Fprint(session.Channel, "\r")	
	return nil


}
