package commands

import (
	"fmt"
	"api/core/net/sessions"
)

func reload(session *sessions.Session, args []string) {
	if !isAdmin(session) {
		fmt.Fprintf(session.Conn, "This Requires Admin!\n\r")
		return
	}

	go Init()
	for _, v := range Commands {
		fmt.Fprintf(session.Conn, "Successfully Reloaded %s : %s | Admin : %t\n\r",v.Name, v.Description, v.Admin)
	}
}

