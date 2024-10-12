package main

import (
	"database/sql"
	"encoding/hex"
	"fmt"
	"log"
	"math/rand"
	"time"

	_ "github.com/mattn/go-sqlite3"
)

var (
	SQL *sql.DB = new(sql.DB)
	err error = nil // Temp
)

// SpawnSQL will open the SQL containing information about the user,attacks & logins.
func SpawnSQL() error {
	SQL, err = sql.Open("sqlite3", Options.Templates.Database.Local)
	if err != nil {
		return err
	}

	if err := SQL.Ping(); err != nil {
		return err
	}

	for item, format := range Spawn {
		if s, err := SQL.Query("SELECT * FROM `" + item + "`"); err == nil && s != nil {
			s.Close(); continue
		}

		statement, err := SQL.Prepare(format)
		if err != nil {
			return err
		}

		defer statement.Close()
		if _, err := statement.Exec(); err != nil {
			return err
		}

		switch item {
		case "users":
			random := make([]byte, 8)
			var Password string = "root"
			if _, err := rand.Read(random); err == nil {
				Password = hex.EncodeToString(random)
			}

			// Querys the insert into the SQL database
			if s, err := SQL.Exec("INSERT INTO `users` (`username`,`password`,`admin`,`api`,`reseller`,`newuser`,`maxtime`,`cooldown`,`conns`,`max_daily`,`expiry`) VALUES ('root', ?, 1,1,0,1,800,30,2,50, ?);", Password, time.Now().Add(1000 * 24 * time.Hour).Unix()); err == nil && s != nil {
				log.Printf("\x1b[48;5;10m\x1b[38;5;16m Success \x1b[0m User inserted! ('root':'%s')", Password)
				continue
			} else {
				panic(err)
			}
		}
	}

	return nil
}

// spawn will hold all the tables which are used inside the database like User,Attack & Logins.
var Spawn map[string]string = map[string]string{
	"users": "CREATE TABLE `users` (`id` INTEGER PRIMARY KEY AUTOINCREMENT,`username` TEXT NOT NULL,`password` TEXT NOT NULL,`admin` INTEGER NOT NULL,`reseller` INTEGER NOT NULL,`newuser` INTEGER NOT NULL,`api` INTEGER NOT NULL,`maxtime` INTEGER NOT NULL,`cooldown` INTEGER NOT NULL,`conns` INTEGER NOT NULL,`max_daily` INTEGER NOT NULL,`expiry` INTEGER NOT NULL);",
	"attacks": "CREATE TABLE `attacks` (`target` TEXT NOT NULL,`duration` INTEGER NOT NULL,`flags` TEXT NOT NULL,`sent` INTEGER NOT NULL,`finish` INTEGER NOT NULL,`user` TEXT NOT NULL,`devices` INTEGER NOT NULL);",
}


// AttackLogs are saved to the database
type AttackLog struct {
	Target   string
	Duration int
	Flags	 string
	Sent	 int64
	Finish	 int64
	User 	 string
	Devices  int
}

// LogAttack will log the attack to the database
func LogAttack(log *AttackLog) error {
	_, err := SQL.Exec("INSERT INTO `attacks` (`target`, `duration`, `flags`, `sent`, `finish`, `user`, `devices`) VALUES (?, ?, ?, ?, ?, ?, ?)", log.Target, log.Duration, log.Flags, log.Sent, log.Finish, log.User, log.Devices)
	return err
}

// User format structure for queries
type User struct {
	ID 		 int
	Username string
	Password string
	Admin 	 bool
	Maxtime  int
	Cooldown int
	Conns    int
	API	 	 bool
	NewUser  bool
	Reseller bool
	MaxDaily int
	Expiry   int64
}

// FindUser will try to find the user inside the database.
func FindUser(user string) (*User, error) {
	statement, err := SQL.Prepare("SELECT `id`,`username`,`password`,`admin`, `api`,`reseller`,`newuser`,`maxtime`,`cooldown`,`conns`,`max_daily`,`expiry` FROM `users` WHERE `username` = ?",)
	if err != nil {
		return nil, err
	}

	defer statement.Close()
	query := statement.QueryRow(user)
	if query.Err() != nil {
		return nil, err
	}

	var User *User = new(User)
	if err := query.Scan(&User.ID, &User.Username, &User.Password, &User.Admin, &User.API, &User.Reseller, &User.NewUser, &User.Maxtime, &User.Cooldown, &User.Conns, &User.MaxDaily, &User.Expiry); err != nil {
		return nil, err
	}
	
	return User, nil
}

// CreateUser will attempt to create a new user inside the database.
func CreateUser(user *User) error {
	_, err := SQL.Exec("INSERT INTO `users` (`username`,`password`,`admin`,`api`,`reseller`,`newuser`,`maxtime`,`cooldown`,`conns`,`max_daily`,`expiry`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", user.Username, user.Password, user.Admin, user.API, user.Reseller, user.NewUser, user.Maxtime, user.Cooldown, user.Conns, user.MaxDaily, user.Expiry)
	return err
}

// ModifyField will modify the needed field
func ModifyField(user *User, field string, value interface{}) error {
	_, err := SQL.Exec(fmt.Sprintf("UPDATE `users` SET `%s` = ? WHERE `username` = ?", field), value, user.Username)
	return err
}

// GetUsers will fetch all the users inside the database
func GetUsers() ([]*User, error) {
	users, err := SQL.Query("SELECT `id`,`username`,`password`,`admin`, `api`,`reseller`,`newuser`,`maxtime`,`cooldown`,`conns`,`max_daily`,`expiry` FROM `users`")
	if err != nil {
		return make([]*User, 0), err
	}

	Users := make([]*User, 0)

	defer users.Close()
	for users.Next() {
		var account *User = new(User)
		if err := users.Scan(&account.ID, &account.Username, &account.Password, &account.Admin, &account.API, &account.Reseller, &account.NewUser, &account.Maxtime, &account.Cooldown, &account.Conns, &account.MaxDaily, &account.Expiry); err != nil {
			return make([]*User, 0), err
		}

		Users = append(Users, account)
	}

	return Users, nil
}

// OngoingAttacks will fetch all the ongoing attacks
func OngoingAttacks(frame time.Time) ([]AttackLog, error) {
	attacks, err := SQL.Query("SELECT `target`, `duration`, `flags`, `sent`, `finish`, `user`, `devices` FROM `attacks` WHERE `finish` > ?", frame.Unix())
	if err != nil {
		return nil, err
	}

	defer attacks.Close()
	var running []AttackLog = make([]AttackLog, 0)
	for attacks.Next() {
		var attack *AttackLog = new(AttackLog)
		if err := attacks.Scan(&attack.Target, &attack.Duration, &attack.Flags, &attack.Sent, &attack.Finish, &attack.User, &attack.Devices); err != nil {
			return nil, err
		}

		running = append(running, *attack)
	}

	return running, nil
}

// UserOngoingAttacks will fetch all the ongoing attacks
func UserOngoingAttacks(user string, frame time.Time) ([]AttackLog, error) {
	attacks, err := SQL.Query("SELECT `target`, `duration`, `flags`, `sent`, `finish`, `user`, `devices` FROM `attacks` WHERE `finish` > ? AND `user` = ?", frame.Unix(), user)
	if err != nil {
		return nil, err
	}

	defer attacks.Close()
	var running []AttackLog = make([]AttackLog, 0)
	for attacks.Next() {
		var attack *AttackLog = new(AttackLog)
		if err := attacks.Scan(&attack.Target, &attack.Duration, &attack.Flags, &attack.Sent, &attack.Finish, &attack.User, &attack.Devices); err != nil {
			return nil, err
		}

		running = append(running, *attack)
	}

	return running, nil
}

func RemoveUser(user string) error {
	_, err := SQL.Exec("DELETE FROM `users` WHERE `username` = ?", user)
	return err
}

func CleanAttacksForUser(user string) error {
	_, err := SQL.Exec("DELETE FROM `attacks` WHERE `user` = ?", user)
	return err
}