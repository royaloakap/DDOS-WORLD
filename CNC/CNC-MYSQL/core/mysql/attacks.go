package database

import (
	"database/sql"
	"errors"
	"time"
)



type Attack struct {
	ID			int
	Method		string
	Target		string
	Port			int
	Duration 		int

	Username		string

	End			int64
	Created 		int64
}

/*

CREATE TABLE `attacks` (
     `ID` int(10) unsigned NOT NULL AUTO_INCREMENT, 
     `Username` varchar(20) NOT NULL, 
     `Target` varchar(255) NOT NULL, 
     `Method` varchar(20) NOT NULL, 
     `Port` int(11) NOT NULL, 
     `Duration` int(11) NOT NULL, 
     `End` bigint(20) NOT NULL, 
     `Created` bigint(20) NOT NULL, 
     PRIMARY KEY (`ID`),  
     KEY `username` (`Username`)
);
*/

type Running struct {
	Running		int
}

func LogAttack(Attacks *Attack) (bool, error) {
	_, error := Database.Query("INSERT INTO `attacks` (`ID`, `Username`, `Target`, `Method`, `Port`, `Duration`, `End`, `Created`) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?)",
		Attacks.Username,
		Attacks.Target,
		Attacks.Method,
		Attacks.Port,
		Attacks.Duration,
		Attacks.End,
		Attacks.Created,
	); if error != nil {
		return false, error
	}
	return true, nil
}

func GetRunningUser(User string) (int, error) {
	var Active Running
	error := Database.QueryRow("SELECT COUNT(*) FROM `attacks` WHERE `Username` = ? AND `End` > ?", User, time.Now().Unix()).Scan(&Active.Running); if error != nil {
		return 0, error
	}

	return Active.Running, nil
}

func GetRunning() (int, error) {
	var Active Running
	error := Database.QueryRow("SELECT COUNT(*) FROM `attacks` WHERE `End` > ?", time.Now().Unix()).Scan(&Active.Running); if error != nil {
		return 0, error
	}


	return Active.Running, nil
}

func AlreadyUnderAttack(User string, Target string) (*Attack, error) {
	var RunningDetails Attack
	error := Database.QueryRow("SELECT `ID`, `Username`, `Target`, `Method`, `Port`, `Duration`, `End`, `Created` FROM `attacks` WHERE `Target` = ? AND `End` > ?", Target, time.Now().Unix()).Scan(
		&RunningDetails.ID,
		&RunningDetails.Username,
		&RunningDetails.Target,
		&RunningDetails.Method,
		&RunningDetails.Port,
		&RunningDetails.Duration,
		&RunningDetails.End,
		&RunningDetails.Created,
	); if error != nil {
		return nil, nil
	}

	return &RunningDetails, nil
}

func AmmountSent() int {
	var Active Running
	error := Database.QueryRow("SELECT COUNT(*) FROM `attacks`").Scan(&Active.Running); if error != nil {
		return 0
	}


	return Active.Running
}

func MySent(user string) int {
	var Active Running
	error := Database.QueryRow("SELECT COUNT(*) FROM `attacks` WHERE `username` = ?", user).Scan(&Active.Running); if error != nil {
		return 0
	}


	return Active.Running
}

func Ongoing() ([]*Attack, error) {

	var AttackRunning []*Attack

	var rows *sql.Rows = nil
	var err error = errors.New("")


	if Database == nil {
		return nil, errors.New("`Database Query is valued as a nil pointer`\r\n")
	}

	rows, err = Database.Query("SELECT `ID`, `Username`, `Target`, `Method`, `Port`, `Duration`, `End`, `Created` FROM `attacks` WHERE `End` > ?", time.Now().Unix())

	if err != nil {
		if err == sql.ErrNoRows {
			return nil, nil
		}

		return AttackRunning, err
	}

	defer rows.Close()

	for rows.Next() {
		Attacks := &Attack{}
		if err := scanOngoing(rows, Attacks); err != nil {
			continue
		}

		AttackRunning = append(AttackRunning, Attacks)
	}

	return AttackRunning, nil
}

func MyAttacking(User string) ([]*Attack, error) {

	var AttackRunning []*Attack

	var rows *sql.Rows
	var err error


	rows, err = Database.Query("SELECT `ID`, `Username`, `Target`, `Method`, `Port`, `Duration`, `End`, `Created` FROM `attacks` WHERE `End` > ? AND `username` = ?", time.Now().Unix(), User)
	if err != nil {
		if err == sql.ErrNoRows {
			return nil, nil
		}

		return AttackRunning, err
	}

	defer rows.Close()

	for rows.Next() {
		Attacks := &Attack{}
		if err := scanOngoing(rows, Attacks); err != nil {
			continue
		}

		AttackRunning = append(AttackRunning, Attacks)
	}

	return AttackRunning, nil
}

func scanOngoing(row *sql.Rows, Attacking *Attack) error {

	return row.Scan(
		&Attacking.ID, 
		&Attacking.Username, 
		&Attacking.Target, 
		&Attacking.Method, 
		&Attacking.Port, 
		&Attacking.Duration, 
		&Attacking.End, 
		&Attacking.Created, 
	)
}