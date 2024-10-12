package commands

import (
	"fmt"
	"api/core/database"
	"api/core/net/sessions"
	"time"
	"bufio"
	"strconv"
	"strings"
)

func users(session *sessions.Session, args []string) {
	users, err := database.Container.GetUsers()
	if err != nil {
		fmt.Fprintf(session.Conn, "Error: %v\n\r", err)
		return
	}

	fmt.Fprintf(session.Conn, "+------------------------------------------------------------------------------+\n\r")
	fmt.Fprintf(session.Conn, "+   ID    |      Username       | Concurrents | Duration | Balance |   Expiry  +\n\r")
	fmt.Fprintf(session.Conn, "+------------------------------------------------------------------------------+\n\r")

	for _, user := range users {
		expiryTime := time.Unix(user.Expiry, 0)
		dateString := expiryTime.Format("2006-01-02")
		fmt.Fprintf(session.Conn, "%-10d| %-20s| %-12d| %-9d| %-8d| %s\n\r",
			user.ID, user.Username, user.Concurrents,
			user.Duration, user.Balance, dateString)
	}
	fmt.Fprintf(session.Conn, "+------------------------------------------------------------------------------+\n\r")
}

func user(session *sessions.Session, args []string) {
    // Prompt for the username
    fmt.Fprintf(session.Conn, "Username: ")
    username, err := bufio.NewReader(session.Conn).ReadString('\n')
    if err != nil {
        fmt.Fprintf(session.Conn, "Error reading input: %v\n\r", err)
        return
    }
    username = strings.TrimSpace(username)

    user, err := database.Container.GetUser(username)
    if err != nil {
        fmt.Fprintf(session.Conn, "Error fetching user: %v\n\r", err)
        return
    }
    if user == nil {
        fmt.Fprintf(session.Conn, "User not found\n\r")
        return
    }

    fmt.Fprintf(session.Conn, "User found:\n\r")
    fmt.Fprintf(session.Conn, "ID: %d\n\r", user.ID)
    fmt.Fprintf(session.Conn, "Username: %s\n\r", user.Username)
    fmt.Fprintf(session.Conn, "Concurrents: %d\n\r", user.Concurrents)
    fmt.Fprintf(session.Conn, "Duration: %d\n\r", user.Duration)
    fmt.Fprintf(session.Conn, "Balance: %d\n\r", user.Balance)
    fmt.Fprintf(session.Conn, "Expiry: %s\n\r", time.Unix(user.Expiry, 0).Format("2006-01-02"))
	fmt.Fprintf(session.Conn, "\n\r")
}

func editUser(session *sessions.Session, args []string) {
    fmt.Fprintf(session.Conn, "Username: ")
    username, err := bufio.NewReader(session.Conn).ReadString('\n')
    if err != nil {
        fmt.Fprintf(session.Conn, "Error reading input: %v\n\r", err)
        return
    }
    username = strings.TrimSpace(username)

    user, err := database.Container.GetUser(username)
    if err != nil {
        fmt.Fprintf(session.Conn, "Error fetching user: %v\n\r", err)
        return
    }
    if user == nil {
        fmt.Fprintf(session.Conn, "User not found\n\r")
        return
    }

    fmt.Fprintf(session.Conn, "User found:\n\r")
    fmt.Fprintf(session.Conn, "ID: %d\n\r", user.ID)
    fmt.Fprintf(session.Conn, "Username: %s\n\r", user.Username)
    fmt.Fprintf(session.Conn, "Concurrents: %d\n\r", user.Concurrents)
    fmt.Fprintf(session.Conn, "Duration: %d\n\r", user.Duration)
    fmt.Fprintf(session.Conn, "Balance: %d\n\r", user.Balance)
    fmt.Fprintf(session.Conn, "Expiry: %s\n\r", time.Unix(user.Expiry, 0).Format("2006-01-02"))
    fmt.Fprintf(session.Conn, "\n\r")

    fmt.Fprintf(session.Conn, "Enter new concurrents: ")
    newConcurrentsStr, err := bufio.NewReader(session.Conn).ReadString('\n')
    if err != nil {
        fmt.Fprintf(session.Conn, "Error reading input: %v\n\r", err)
        return
    }
    newConcurrentsStr = strings.TrimSpace(newConcurrentsStr)
    newConcurrents, err := strconv.Atoi(newConcurrentsStr)
    if err != nil {
        fmt.Fprintf(session.Conn, "Invalid value for concurrents\n\r")
        return
    }

	fmt.Fprintf(session.Conn, "Enter new Duration: ")
	durationStr, err := bufio.NewReader(session.Conn).ReadString('\n')
	if err != nil {
		fmt.Fprintf(session.Conn, "Error reading input: %v\n\r", err)
		return
	}
	durationStr = strings.TrimSpace(durationStr)
	Duration, err := strconv.Atoi(durationStr)
	if err != nil {
		fmt.Fprintf(session.Conn, "Invalid value for concurrents\n\r")
		return
	}

	fmt.Fprintf(session.Conn, "Enter new Balance: ")
	BalanceStr, err := bufio.NewReader(session.Conn).ReadString('\n')
	if err != nil {
		fmt.Fprintf(session.Conn, "Error reading input: %v\n\r", err)
		return
	}
	BalanceStr = strings.TrimSpace(BalanceStr)
	Balance, err := strconv.Atoi(BalanceStr)
	if err != nil {
		fmt.Fprintf(session.Conn, "Invalid value for concurrents\n\r")
		return
	}
    // Update user fields
    user.Concurrents = newConcurrents
	user.Duration = Duration
	user.Balance = Balance

    // Call the method to update the user in the database
    err = database.Container.UpdateUser(user)
    if err != nil {
        fmt.Fprintf(session.Conn, "Error updating user: %v\n\r", err)
        return
    }

    fmt.Fprintf(session.Conn, "User updated successfully\n\r")
}
