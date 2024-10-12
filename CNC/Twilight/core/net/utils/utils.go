package utils

import (
	"fmt"
	"net"
)

func Print(conn net.Conn, str ...interface{}) {
	fmt.Fprint(conn, fmt.Sprint(str...))
}
