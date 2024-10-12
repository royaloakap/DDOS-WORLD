package database

import (
	"database/sql"
	"log"
	"os"
	"time"
)

var (
	Container = new(Instance)
	logger    = log.New(os.Stderr, "[database] ", log.Ltime|log.Lshortfile)
)

type Query interface{ Scan(...any) error }

type Instance struct {
	Connected time.Time

	conn *sql.DB
}
