package database

import (
	"api/core/models/plans"
	"api/core/models/ranks"
	"bytes"
	"database/sql"
	"errors"
	"log"
	"time"
	"encoding/base64"
	"encoding/json"
)

type User struct {
	ID                                      int
	Username                                string
	Key, Salt                               []byte
	ranks, Membership                       string
	Ranks                                   []*ranks.Rank
	Concurrents, Servers, Duration, Balance int
	Expiry                                  int64
}

var (
	ErrDuplicateUser = errors.New("duplicate user")
	ErrUserNotFound  = errors.New("user couldn't be found in the database")
	ErrInvalidInput  = errors.New("invalid ticket data")
)

func (conn *Instance) NewUser(user *User) (err error) {
	if user, err := conn.GetUser(user.Username); err == nil && user != nil {
		return ErrDuplicateUser
	}
	user.Salt = NewSalt(16)
	user.Key = NewHash(user.Key, user.Salt)
	user.ranks = user.NewRoles()
	stmt, err := conn.conn.Prepare("INSERT INTO `users` (`id`, `username`, `key`, `salt`, `roles`, `expiry`, `concurrents`, `servers`, `duration`, `balance`, `membership`) VALUES (NULL, ?,?,?,?,?,?,?,?,?,?)")
	if err != nil {
		return err
	}
	defer stmt.Close()
	if _, err := stmt.Exec(user.Username, user.Key, user.Salt, user.ranks, user.Expiry, user.Concurrents, user.Servers, user.Duration, user.Balance, user.Membership); err != nil {
		return err
	}
	return
}


func (conn *Instance) UserUpdateRank(user *User, ranks []*ranks.Rank, addon *plans.Addon) error {
    log.Printf("Updating roles for user %s\n", user.Username)

    // Prepare the SQL statement
    stmt, err := conn.conn.Prepare("UPDATE `users` SET `balance` = ?, `roles` = ? WHERE `username` = ?")
    if err != nil {
        logger.Println("Error preparing SQL statement:", err)
        return err
    }
    defer stmt.Close()

    // Convert ranks to JSON string
    ranksJSON, err := json.Marshal(ranks)
    if err != nil {
        logger.Println("Error marshalling ranks to JSON:", err)
        return err
    }
	user.Balance -= addon.Price
    // Encode JSON data to base64
    encodedJSON := base64.RawStdEncoding.EncodeToString(ranksJSON)

    // Execute the update query
    _, err = stmt.Exec(user.Balance, encodedJSON, user.Username)
    if err != nil {
        logger.Println("Error executing SQL statement:", err)
        return err
    }
    return nil
}


func (conn *Instance) GetUser(user string) (*User, error) {
	stmt, err := conn.conn.Prepare("SELECT `id`, `username`, `key`, `salt`, `roles`, `expiry`, `concurrents`, `servers`, `duration`, `balance`, `membership` FROM `users` where `username` = ?")
	if err != nil {
		return nil, err
	}
	defer stmt.Close()
	return conn.scanUser(stmt.QueryRow(user))
}

func (conn *Instance) GetUserID(username string) (int, error) {
    var userID int
    stmt, err := conn.conn.Prepare("SELECT `id` FROM `users` WHERE `username` = ?")
    if err != nil {
        return 0, err
    }
    defer stmt.Close()

    // Execute the query and scan the result into userID
    err = stmt.QueryRow(username).Scan(&userID)
    if err != nil {
        if err == sql.ErrNoRows {
            return 0, ErrUserNotFound
        }
        return 0, err
    }

    return userID, nil
}


func (conn *Instance) GetUserByID(id int) (*User, error) {
	stmt, err := conn.conn.Prepare("SELECT `id`, `username`, `key`, `salt`, `roles`, `expiry`, `concurrents`, `servers`, `duration`, `balance`, `membership` FROM `users` where `id` = ?")
	if err != nil {
		return nil, err
	}
	defer stmt.Close()
	return conn.scanUser(stmt.QueryRow(id))
}

func (conn *Instance) GetUsers() ([]*User, error) {
	stmt, err := conn.conn.Prepare("SELECT `id`, `username`, `key`, `salt`, `roles`, `expiry`, `concurrents`, `servers`, `duration`, `balance`, `membership` FROM `users`")
	if err != nil {
		return nil, err
	}
	rows, err := stmt.Query()
	if err != nil {
		return nil, err
	}
	var users []*User
	for rows.Next() {
		user, err := conn.scanUser(rows)
		if err != nil {
			continue
		}
		users = append(users, user)
	}
	return users, nil
}

func (conn *Instance) UpdateUserPlan(user *User, plan *plans.Plan) error {
	stmt, err := conn.conn.Prepare("UPDATE `users` SET `roles` = ?, `expiry` = ?, `concurrents` = ?, `servers` = ?, `duration` = ?, `balance` = ?, `membership` = ? WHERE `username` = ?")
	if err != nil {
		return err
	}
	if plan.API {
		user.Ranks = append(user.Ranks, ranks.Internal["api"])
	}
	user.ranks = user.NewRoles()
	user.Balance -= plan.Price
	user.Membership = "premium"
	if _, err := stmt.Exec(user.ranks, time.Now().Add((time.Duration(plan.Expiry)*time.Hour)*24).Unix(), plan.Conns, 5, plan.Duration, user.Balance, user.Membership, user.Username); err != nil {
		return err
	}
	return nil
}
func (user *User) GetKey() []byte {
    return user.Key
}
func (conn *Instance) scanUser(query Query) (*User, error) {
	user := new(User)
	if err := query.Scan(
		&user.ID,
		&user.Username,
		&user.Key, &user.Salt, &user.ranks, &user.Expiry, &user.Concurrents, &user.Servers, &user.Duration, &user.Balance, &user.Membership,
	); err != nil {
		if errors.Is(err, sql.ErrNoRows) {
			return nil, ErrUserNotFound
		}
		return nil, err
	}
	if err := user.Sync(); err != nil {
		return user, err
	}
	return user, nil
}

func (user *User) IsKey(key []byte) bool {
	return bytes.Equal(NewHash(key, user.Salt), user.Key)
}

func (conn *Instance) Users() (users int) {
	stmt, err := conn.conn.Prepare("SELECT * from `users`")
	if err != nil {
		return 0
	}
	defer stmt.Close()
	result, err := stmt.Query()
	if err != nil {
		logger.Println("GlobalUsers(): error occured while executing statement \"" + err.Error() + "\"")
		return 0
	}
	for result.Next() {
		users++
	}
	return
}

func (conn *Instance) UpdateUser(user *User) error {
    stmt, err := conn.conn.Prepare("UPDATE `users` SET `roles` = ?, `expiry` = ?, `concurrents` = ?, `servers` = ?, `duration` = ?, `balance` = ? WHERE `username` = ?")
    if err != nil {
        return err
    }
    defer stmt.Close()
	user.ranks = user.NewRoles()
    // Execute the update query
    if _, err := stmt.Exec(user.ranks, user.Expiry, user.Concurrents, user.Servers, user.Duration, user.Balance, user.Username); err != nil {
        return err
    }

    return nil
}

func (conn *Instance) DeleteUser(username string, userID int) error {
    // Prepare the delete statement
    stmt, err := conn.conn.Prepare("DELETE FROM `users` WHERE `username` = ?")
    if err != nil {
        return err
    }
    defer stmt.Close()

    // Execute the delete statement
    res, err := stmt.Exec(username)
    if err != nil {
        return err
    }

    // Check if any rows were affected
    rowsAffected, err := res.RowsAffected()
    if err != nil {
        return err
    }
    if rowsAffected == 0 {
        return ErrUserNotFound
    }

	    // Prepare the delete statement
		stmt1, err := conn.conn.Prepare("DELETE FROM `tickets` WHERE `user_id` = ?")
		if err != nil {
			return err
		}
		defer stmt1.Close()
	
		// Execute the delete statement
		res, err = stmt1.Exec(userID)
		if err != nil {
			return err
		}

    return nil
}