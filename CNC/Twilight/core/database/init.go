package database

import (
	"database/sql"
	"time"

	_ "github.com/mattn/go-sqlite3"
)

func New() error {
	Container.Connected = time.Now()
	db, err := sql.Open("sqlite3", "assets/database.db")
	if err != nil {
		return err
	}
	if err := db.Ping(); err != nil {
		return err
	}
	Container.conn = db
	logger.Println("New(): succesfully connected to database")
	return nil
}
