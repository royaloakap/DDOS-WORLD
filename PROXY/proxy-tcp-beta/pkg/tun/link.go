package tun

import (
	"errors"
	"fmt"
	"regexp"
	"strconv"

	"golang.org/x/crypto/ssh"
)

type link struct {
	username        string
	host            string
	port            int
	sshClientConfig ssh.ClientConfig
	sshClient       *ssh.Client
}

var userHostPortRegex = regexp.MustCompile(`^([a-z0-9]+)@([^:]+):(\d+)`)

// errors
var (
	ErrInvalidFormat = errors.New("invalid format")
)

func parseLink(s string) (link, error) {
	uhp := userHostPortRegex.FindStringSubmatch(s)
	if len(uhp) != 4 {
		return link{}, ErrInvalidFormat
	}

	port, err := strconv.ParseInt(uhp[3], 10, 64)
	if err != nil {
		return link{}, fmt.Errorf("%w: %v", ErrInvalidFormat, err)
	}

	return link{
		username: uhp[1],
		host:     uhp[2],
		port:     int(port),
	}, nil
}

func (l link) HostPort() string {
	return fmt.Sprintf("%s:%d", l.host, l.port)
}
