package database

import (
	"api/core/models/floods"
	"time"
)
func (conn *Instance) NewBlacklist(host string) error {
    stmt, err := conn.conn.Prepare("INSERT INTO `blacklist` (`host`) VALUES (?)")
    if err != nil {
        logger.Println("NewNews(): error preparing SQL statement:", err)
        return err
    }
    defer stmt.Close()

    _, err = stmt.Exec(host)
    if err != nil {
        logger.Println("NewNews(): error executing SQL statement:", err)
        return err
    }

    return nil
}

func (conn *Instance) GetAllBlacklists() ([]string, error) {
    var blacklists []string

    rows, err := conn.conn.Query("SELECT `host` FROM `blacklists`")
    if err != nil {
        logger.Println("GetAllBlacklists(): error occurred while querying database: ", err)
        return nil, err
    }

    defer rows.Close()

    for rows.Next() {
        var host string
        if err := rows.Scan(&host); err != nil {
            logger.Println("GetAllBlacklists(): error occurred while scanning rows: ", err)
            return nil, err
        }
        blacklists = append(blacklists, host)
    }

    if err := rows.Err(); err != nil {
        logger.Println("GetAllBlacklists(): error occurred while iterating through rows: ", err)
        return nil, err
    }

    return blacklists, nil
}


func (conn *Instance) NewAttack(user *User, attack *floods.Attack) (int, error) {
	stmt, err := conn.conn.Prepare("INSERT INTO `attacks` (`id`, `method`, `target`, `port`, `threads`, `pps`, `parent`,`stopped`, `duration`,`type`, `date`) VALUES (NULL, ?,?,?,?,?,?,?,?,?,?)")
	if err != nil {
		logger.Println("user/NewAttack(): error occured while preparing statement \"" + err.Error() + "\"")
		return 0, err
	}
	result, err := stmt.Exec(attack.Name, attack.Target, attack.Port, attack.Threads, attack.PPS, user.ID, 0, attack.Duration, attack.Type, attack.Created)
	if err != nil {
		logger.Println("user/NewAttack(): error occured while executing statement \"" + err.Error() + "\"")
		return 0, err
	}
	id, err := result.LastInsertId()
	if err != nil {
		logger.Println("user/NewAttack()/LastInsertID(): error occured while getting the id \"" + err.Error() + "\"")
		return 0, err
	}
	return int(id), nil
}

func (conn *Instance) Stop(user *User, target string) error {
	stmt, err := conn.conn.Prepare("UPDATE `attacks` SET `stopped` = 1 WHERE `parent` = ? AND `target` = ?")
	if err != nil {
		logger.Println("user/Stop(): error occured while preparing statement \"" + err.Error() + "\"")
		return err
	}
	_, err = stmt.Exec(user.ID, target)
	if err != nil {
		logger.Println("user/Stop(): error occured while executing statement \"" + err.Error() + "\"")
		return err
	}
	return nil
}

func (conn *Instance) GetRunning(user *User) ([]*floods.Attack, error) {
	stmt, err := conn.conn.Prepare("SELECT * FROM `attacks` WHERE (date+duration) > ? AND `parent` = ?")
	if err != nil {
		logger.Println("user/GetRunning(): error occured while preparing statement \"" + err.Error() + "\"")
		return nil, err
	}
	result, err := stmt.Query(time.Now().Unix(), user.ID)
	if err != nil {
		logger.Println("user/GetRunning(): error occured while executing statement \"" + err.Error() + "\"")
		return nil, err
	}
	running := make([]*floods.Attack, 0)
	for result.Next() {
		flood := new(floods.Attack)
		flood.Method = new(floods.Method)
		if err := result.Scan(&flood.ID, &flood.Method.Name, &flood.Target, &flood.Port, &flood.Threads, &flood.PPS, &flood.Parent, &flood.Duration, &flood.Type, &flood.Stopped, &flood.Created); err != nil {
			logger.Println("user/GetRunning(): failed to scan flood! \"" + err.Error() + "\"")
		}
		running = append(running, flood)
	}
	return running, nil
}

func (conn *Instance) DailyAttacks() (result int) {
	date := time.Date(time.Now().Year(), time.Now().Month(), time.Now().Day(), 0, 0, 0, 0, time.Local).Unix()
	stmt, err := conn.conn.Prepare("SELECT COUNT(*) FROM `attacks` WHERE date > ?")
	if err != nil {
		logger.Println("database/dailyAttacks(): error occured while preparing statement \"" + err.Error() + "\"")
		return 0
	}
	if err := stmt.QueryRow(date).Scan(&result); err != nil {
		return 0
	}
	return

}

func (conn *Instance) GlobalRunning() (ongoing int) {
	stmt, err := conn.conn.Prepare("SELECT * FROM `attacks` WHERE (date+duration) > ? ")
	if err != nil {
		logger.Println("user/GetRunning(): error occured while preparing statement \"" + err.Error() + "\"")
		return 0
	}
	result, err := stmt.Query(time.Now().Unix())
	if err != nil {
		logger.Println("user/GetRunning(): error occured while executing statement \"" + err.Error() + "\"")
		return 0
	}
	for result.Next() {
		ongoing++
	}
	return
}

func (conn *Instance) GlobalRunningType(sType int) (ongoing int) {
	stmt, err := conn.conn.Prepare("SELECT * FROM `attacks` WHERE (date+duration) > ? AND `type` = ? ")
	if err != nil {
		logger.Println("user/GetRunning(): error occured while preparing statement \"" + err.Error() + "\"")
		return 0
	}
	result, err := stmt.Query(time.Now().Unix(), sType)
	if err != nil {
		logger.Println("user/GetRunning(): error occured while executing statement \"" + err.Error() + "\"")
		return 0
	}
	for result.Next() {
		ongoing++
	}
	return
}

func (conn *Instance) Attacks() (ongoing int) {
	stmt, err := conn.conn.Prepare("SELECT * FROM `attacks` ")
	if err != nil {
		logger.Println("user/GlobalAttacks(): error occured while preparing statement \"" + err.Error() + "\"")
		return 0
	}
	result, err := stmt.Query()
	if err != nil {
		logger.Println("user/GlobalAttacks(): error occured while executing statement \"" + err.Error() + "\"")
		return 0
	}
	for result.Next() {
		ongoing++
	}
	return
}

func (conn *Instance) GetFromTo(start, end, atype int) (ongoing int) {
	stmt, err := conn.conn.Prepare("SELECT * FROM `attacks` WHERE `date` > ? AND `date` < ? AND `type` = ? ")
	if err != nil {
		logger.Println("user/GetRunning(): error occured while preparing statement \"" + err.Error() + "\"")
		return 0
	}
	result, err := stmt.Query(start, end, atype)
	if err != nil {
		logger.Println("user/GetRunning(): error occured while executing statement \"" + err.Error() + "\"")
		return 0
	}
	for result.Next() {
		ongoing++
	}
	return
}
