package main

import (
	"database/sql"
	"fmt"
	_ "github.com/go-sql-driver/mysql"
	"github.com/matthewhartstonge/argon2"
	"log"
)

type Database struct {
	db *sql.DB
}

type AccountInfo struct {
	id         int
	username   string
	membership int
	rank       int
	expiry     int
}

func NewDatabase(dbAddr string, dbUser string, dbPassword string, dbName string) *Database {
	db, err := sql.Open("mysql", fmt.Sprintf("%s:%s@tcp(%s)/%s", dbUser, dbPassword, dbAddr, dbName))
	if err != nil {
		log.Panic("Failed to connect.")
		return nil
	}
	log.Println("Connected to database.")
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
	ok, err := argon2.VerifyEncoded([]byte(password), []byte(passwordFromDatabase))
	if err != nil {
		return false, err
	}
	return ok, nil
}

func (this *Database) GetAccountInfo(username string) AccountInfo {
	rows, err := this.db.Query("SELECT id, username, membership, rank, expiry FROM users WHERE username = ?", username)
	if err != nil {
		log.Println(err)
		return AccountInfo{0, "", 0, 0, 0}
	}
	defer rows.Close()
	if !rows.Next() {
		return AccountInfo{0, "", 0, 0, 0}
	}
	var accInfo AccountInfo
	rows.Scan(&accInfo.id, &accInfo.username, &accInfo.membership, &accInfo.rank, &accInfo.expiry)
	return accInfo
}
