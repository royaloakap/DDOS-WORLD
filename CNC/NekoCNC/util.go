package main

import (
	"encoding/json"
	"github.com/gliderlabs/ssh"
	"fmt"
	"time"
	"net/http"
	"net"
	"io/ioutil"
	"strings"
	"github.com/tidwall/gjson"
	"strconv"
)

//func grabcursor(theme string)

func changetheme(theme string) (string, string, string, string, string, string) {
	themconfig, err := ioutil.ReadFile("themes.json")
    if err != nil {
        fmt.Println("ERROR\nconfig.json not found!\n")
    }
    confile := string(themconfig)
    var col1 = (gjson.Get(confile, theme+".colors.0")).String()
    var col2 = (gjson.Get(confile, theme+".colors.1")).String()
    var col3 = (gjson.Get(confile, theme+".colors.2")).String()
    var col4 = (gjson.Get(confile, theme+".colors.3")).String()
    asset := (gjson.Get(confile, theme+".asset")).String()
    var cursor = (gjson.Get(confile, theme+".cursor")).String()
    return asset, col1, col2, col3, col4, cursor
}

func istheme(theme string) bool {
	themconfig, err := ioutil.ReadFile("themes.json")
    if err != nil {
        fmt.Println("ERROR\nconfig.json not found!\n")
    }
    confile := string(themconfig)
    Tdoes := (gjson.Get(confile, theme)).Exists()
    return Tdoes
}

func onlineList(s ssh.Session, P string, D string, G string, B string) {
	 sessionMutex.Lock()
	 s.Write([]byte("\r\x1b[38;5;242m                ╔══════════════════════╗\r\n\x1b[38;5;242m"))
     for i := range sessions {
        fmt.Fprintf(s, "\x1b[38;5;242m                ║ "+B+"%-20s \x1b[38;5;242m║ "+P+"- %.2f Minutes", sessions[i].Username, time.Since(time.Unix(sessions[i].Created, 0)).Minutes())
        s.Write([]byte("\r\n"))
    }
    s.Write([]byte("\r\x1b[38;5;242m                ╚══════════════════════╝\r\n"))
    sessionMutex.Unlock()
    return
}

func lookup(s ssh.Session, host string, P string, D string, G string, B string) {
	ip := net.ParseIP(host)
    if ip == nil {
        fmt.Fprint(s, "\x1b[0mNeko╼➤   "+host+" is not an ip address!\n")
        return
    }
    var jarray map[string]interface{}
	resp, err := http.Get("http://ip-api.com/json/"+host)
	defer resp.Body.Close()
	rebody, err := ioutil.ReadAll(resp.Body)
    if err != nil {
        s.Write([]byte("Neko╼➤   API ERROR\n"))
    }
    lookupin := string(rebody)
    if err := json.Unmarshal([]byte(lookupin), &jarray); err != nil {
        s.Write([]byte("Neko╼➤   API ERROR\n"))
    }//          ║ TimeZone: America/Los_Angeles                        ║
    if jarray["status"] == "success" {
    	lookuparr := map[string]string {

        "${host}": 
        	fmt.Sprintf("%v",jarray["query"]),
        "${country}": 
        	fmt.Sprintf("%v",jarray["country"]),
        "${region}": 
        	fmt.Sprintf("%v",jarray["regionName"]),
        "${city}": 
        	fmt.Sprintf("%v",jarray["city"]),
        "${zip}": 
        	fmt.Sprintf("%v",jarray["zip"]),
        "${lat}": 
        	fmt.Sprintf("%v",jarray["lat"]),
        "${lon}": 
        	fmt.Sprintf("%v",jarray["lon"]),
        "${timezone}": 
        	fmt.Sprintf("%v",jarray["timezone"]),
        "${isp}": 
        	fmt.Sprintf("%v",jarray["isp"]),
        "${org}": 
        	fmt.Sprintf("%v",jarray["org"]),
        "${as}": 
        	fmt.Sprintf("%v",jarray["as"]),

    	}
    	ipgeo := termFx(s.User(), P, D, G, B, assetFetch("lookup"))
    	for oldChar, newChar := range lookuparr {
			ipgeo = strings.ReplaceAll(ipgeo, oldChar, newChar)
		}
    	fmt.Fprint(s, ipgeo)

    }
    return
}

func sshloads(s ssh.Session, P string, D string, G string, B string) {
	loadingneko := termFx(s.User(), P, D, G, B, assetFetch("loading"))
	fmt.Fprintf(s, loadingneko)
	loading_prompt := termFx(s.User(), P, D, G, B, assetFetch("loading_prompt"))
	spinBuf := []string{"-", "/", "|", "\\"}
        /*for i := 0; i < 30; i++ {
            s.Write([]byte("[2K[K[0K[1K[0G"))
            s.Write([]byte("[2K[K[0K[1K[0G             "+loading_prompt+"\x1b[38;5;242m"+spinBuf[i % len(spinBuf)]))
            time.Sleep(time.Duration(50) * time.Millisecond)
        }*/
        spinnythingy := 0
        for i := 0; i < 100; i = i + 4 {
          yoloit := strconv.Itoa(i)+"%"
          s.Write([]byte(fmt.Sprintf("\033]0;Loading ["+spinBuf[spinnythingy]+"]\007")))
          s.Write([]byte("[2K[K[0K[1K[0G             "+loading_prompt+" \x1b[38;5;242m"+yoloit))
          time.Sleep(time.Millisecond * 90)
          spinnythingy = spinnythingy + 1
          if spinnythingy > 3 {
            spinnythingy = 0
          }
        }
	return
}

/*

        if userInfo.admin == 1 && cmd == "sessions" {
            sessionMutex.Lock()
            s.Write([]byte("\r\x1b[38;5;242m                ╔══════════════════════╗\r\n\x1b[38;5;242m"))
            for i := range sessions {
                fmt.Fprintf(s, "\x1b[38;5;242m                ║ "+B+"%-20s \x1b[38;5;242m║ "+P+"- %.2f Minutes", sessions[i].Username, time.Since(time.Unix(sessions[i].Created, 0)).Minutes())
                s.Write([]byte("\r\n"))
            }
            s.Write([]byte("\r\x1b[38;5;242m                ╚══════════════════════╝\r\n"))
            sessionMutex.Unlock()
            continue
        }
*/