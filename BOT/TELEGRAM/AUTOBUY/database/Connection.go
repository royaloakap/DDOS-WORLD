package database

import (
	"Telegram/structs"
	"database/sql"
	_ "modernc.org/sqlite"
)

var DB *sql.DB
var err error

func Connect() {
	DB, err = sql.Open("sqlite", "./database.db")
	CheckError(err)
	_, err = DB.Exec("CREATE TABLE IF NOT EXISTS `Users` (`ID` TEXT NOT NULL UNIQUE, `Plan` TEXT NOT NULL, `Banned` INTEGER NOT NULL DEFAULT 0, `Expiry` TEXT NOT NULL);")
	CheckError(err)
}

func InsertUser(user structs.User) {
	_, err = DB.Exec("INSERT INTO `Users` (`ID`, `Plan`, `Banned`, `Expiry`) VALUES (?, ?, ?, ?);", user.ID, user.Plan.Name, user.Banned, user.Expiry)
	CheckError(err)
}

func UpdateUser(user structs.User) {
	_, err = DB.Exec("UPDATE `Users` SET `Plan` = ?, `Banned` = ?, `Expiry` = ? WHERE `ID` = ?;", user.Plan.Name, user.Banned, user.Expiry, user.ID)
	CheckError(err)
	for i, u := range Users {
		if u.ID == user.ID {
			Users[i] = user
		}
	}
}

func DeleteUser(username string) {
	_, err = DB.Exec("DELETE FROM `Users` WHERE `ID` = ?;", username)
	CheckError(err)
	for i, u := range Users {
		if u.ID == username {
			Users = append(Users[:i], Users[i+1:]...)
		}
	}
}

func SelectUser(username string) structs.User {
	var user structs.User
	err = DB.QueryRow("SELECT * FROM `Users` WHERE `ID` = ?;", username).Scan(&user.ID, &user.Plan.Name, &user.Banned, &user.Expiry)
	user.Plan = GetPlan(user.Plan.Name)
	CheckError(err)
	return user
}

func LoadUsers() {
	rows, err := DB.Query("SELECT * FROM `Users`;")
	CheckError(err)
	defer rows.Close()
	for rows.Next() {
		var user structs.User
		err = rows.Scan(&user.ID, &user.Plan.Name, &user.Banned, &user.Expiry)
		user.Plan = GetPlan(user.Plan.Name)
		CheckError(err)
		Users = append(Users, user)
	}
	err = rows.Err()
	CheckError(err)
}
