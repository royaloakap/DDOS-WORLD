package main

import (
    "io/ioutil"
    "strings"
    "strconv"
    "time"
)

func assetFetch(command  string) (string) {
    headerb, err := ioutil.ReadFile("assets/"+command)
	if err != nil {
		return "Not found\n"
	}
	header := string(headerb)
	return strings.Replace(strings.Replace(header, "\r\n", "\n", -1), "\n", "\r\n", -1)
}

func fileCommand(command  string) (string) {
    headerb, err := ioutil.ReadFile("neko/"+command)
	if err != nil {
		return "Command Not found\n"
	}
	header := string(headerb)
	return strings.Replace(strings.Replace(header, "\r\n", "\n", -1), "\n", "\r\n", -1)
}

func termFx(
			nuser string,
			P string,
            D string,
            G string,
            B string,
            input string,
        	) (string) {
	var maxBots, connCurr, expdate, coolDown, timeLimit, ApitimeLimit, admin string
	maxBots, connCurr, expdate, coolDown, timeLimit, ApitimeLimit, admin = database.valueCheck(nuser)
	if admin == "1" {
        admin = "Admin"
    }else{
        admin = "Regular"
    }
	intexp, err := strconv.Atoi(expdate)
	if err != nil {
      intexp = 0
   	}
   	timun := int64(intexp)
   	t := time.Unix(timun, 0)
    strdat := t.Format(time.UnixDate) 
	expdate = strconv.Itoa((int(intexp) - int(time.Now().Unix())) / 86400)

	replaceChars := map[string]string {

//[+] Ansi shortcuts

        "${P}": 
        	P,
        "${D}": 
        	D,
        "${G}": 
        	G,
        "${B}": 
        	B,
        "${L}":
        	"[38;5;242m",
        "${R}": 
        	"[0m",
        "${clear}":
        	"[H[J",

//[+] User info

        "${username}":
        	nuser,
        "${conns}":
        	connCurr,
        "${timelimit}":
        	timeLimit,
        "${api_timelimit}":
        	ApitimeLimit,
        "${cooldown}":
        	coolDown,
        "${expstr}":
        	strdat,
        "${expint}":
        	expdate,
        "${maxbots}":
        	maxBots,
        "${admin}":
        	admin,

//[+] Functions and channels

        "&(online)":
        	strconv.Itoa(onlineUsers()),
        "&(attacks)":
        	strconv.Itoa(database.allAtks()),
        "&(running)":
        	strconv.Itoa(database.currrentAtks()),
        "&(total)":
        	strconv.Itoa(database.countUsers()),
        "&(infected)":
        	strconv.Itoa(clientList.Count()),

    }


	
    for oldChar, newChar := range replaceChars {
		input = strings.ReplaceAll(input, oldChar, newChar)
	}
	return input

}