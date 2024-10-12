package database

import (
	"database/sql"
	"triton-cnc/core/models/json/build"

	_ "github.com/go-sql-driver/mysql"
)

var Database *sql.DB

func Database_Connect() error {

	db, error := sql.Open("mysql", build.Config.Database.Sql_username+":"+build.Config.Database.Sql_password+"@tcp("+build.Config.Database.Sql_host+")/"+build.Config.Database.Sql_name)
	if error != nil {
		return error
	}


	Database = db

	return nil
}