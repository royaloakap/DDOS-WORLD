package dashboardapi

import (
	"api/core/database"
	"api/core/master/sessions"
	"api/core/models/apis"
	"api/core/models/functions"
	"api/core/models/server"
	"api/core/models/servers"
	"fmt"
	"log"
	"math/rand"
	"net/http"
	"strings"
)
var layer7Total = 0
func init() {
	Route.NewSub(server.NewRoute("/data", func(w http.ResponseWriter, r *http.Request) {
		if strings.ToLower(r.Method) == "post" {
			ok, session := sessions.IsLoggedIn(w, r)
			if !ok {
				http.Redirect(w, r, "/login", http.StatusTemporaryRedirect)
				return
			}

			type serverStruct struct {
				ID             int     "json:\"id\""
				Name           string  "json:\"name\""
				Type           string  "json:\"type\""
				Slots          int     "json:\"slots\""
				Status         string  "json:\"status\""
				RunningAttacks int     "json:\"runningAttacks\""
				Load           float64 "json:\"load\""		
			}
			type Data struct {
				UserInfo struct {
					ID               int    `json:"id"`
					Username         string `json:"username"`
					Membership       int    `json:"membership"`
					MembershipExpire int64  `json:"membership_expire"`
					Concurrents      int    `json:"concurrents"`
					Maxboot          int    `json:"maxboot"`
					Balance          int    `json:"balance"`
				} `json:"userInfo"`
				News               []*database.News
				ServersLayer4      []serverStruct `json:"serversLayer4"`
				ServersLayer7      []serverStruct `json:"serversLayer7"`
				UserCount          int            `json:"userCount"`
				AttackCount        int            `json:"attackCount"`
				RunningAttackCount int            `json:"runningAttackCount"`
				OnlineUserCount    int            `json:"onlineUserCount"`
				NetworkInfo        struct {
					Layer4      int `json:"Layer4"`
					Layer7      int `json:"Layer7"`
					Layer4Total int `json:"Layer4Total"`
					Layer7Total int `json:"Layer7Total"`
				} `json:"networkInfo"`
			}
			d := new(Data)
			news, err := database.Container.GetNews()
			if err != nil {
				log.Println(err)
				return
			}
			d.UserInfo = struct {
				ID               int    "json:\"id\""
				Username         string "json:\"username\""
				Membership       int    "json:\"membership\""
				MembershipExpire int64  "json:\"membership_expire\""
				Concurrents      int    "json:\"concurrents\""
				Maxboot          int    "json:\"maxboot\""
				Balance          int    "json:\"balance\""
			}{
				ID:       session.ID,
				Username: session.Username,
				Membership: func() int {
					fmt.Println(session.Ranks)
					if session.HasPermission("admin") {
						return 5
					}
					switch session.Servers {
					case 0, 1, 2:
						return 0
					case 3, 4, 5:
						return 1
					case 6, 7, 8:
						return 2
					case 9, 10, 11:
						return 3
					default:
						return 4
					}
				}(),
				MembershipExpire: session.User.Expiry,
				Concurrents:      session.Concurrents,
				Maxboot:          session.Duration,
				Balance:          session.Balance,
			}
			d.News = news
			d.UserCount = database.Container.Users()
			d.AttackCount = database.Container.Attacks()
			d.RunningAttackCount = database.Container.GlobalRunning()
			d.OnlineUserCount = rand.Intn(20-3) + 3
			d.NetworkInfo = struct {
				Layer4      int "json:\"Layer4\""
				Layer7      int "json:\"Layer7\""
				Layer4Total int "json:\"Layer4Total\""
				Layer7Total int "json:\"Layer7Total\""
			}{
				Layer4:      database.Container.GlobalRunningType(1),
				Layer7:      database.Container.GlobalRunningType(2),
				Layer4Total: servers.Slots()[1] + apis.Slots4(),
				Layer7Total: servers.Slots()[2] + apis.Slots7(),
			}
			d.ServersLayer4 = func() []serverStruct {
				var servs []serverStruct = make([]serverStruct, 0)
				var i = 0
				for _, server := range servers.Servers {
					if server.Type == 1 {
						servs = append(servs, serverStruct{
							ID:             i,
							Name:           server.Name,
							Slots:          server.Slots,
							Status:         "Online",
							Type:           "Layer4",
							RunningAttacks: server.Running(),
							Load:           server.Load(),
						})
					}
				}
				for _, api := range apis.Apis {
					if api.Type == "Layer4" {
					servs = append(servs, serverStruct{
						ID:             i,
						Name:           api.Name,
						Slots:          api.Slots,
						Status:         "Online",
						Type:           "Layer4",
						RunningAttacks: api.Running(),
						Load:           api.Load(),
					})
				 }
				}
				return servs
			}()
			d.ServersLayer7 = func() []serverStruct {
				var servs []serverStruct = make([]serverStruct, 0)
				var i = 0
				for _, server := range servers.Servers {
					if server.Type == 2 { // Check if it's Layer 7
						servs = append(servs, serverStruct{
							ID:             i,
							Name:           server.Name,
							Slots:          server.Slots,
							Status:         "Online",
							Type:			"Layer7",
							RunningAttacks: server.Running(),
							Load:           server.Load(),
						})
					}
				}
				for _, api := range apis.Apis {
					if api.Type == "Layer7" {
					servs = append(servs, serverStruct{
						ID:             i,
						Name:           api.Name,
						Slots:          api.Slots,
						Status:         "Online",
						Type:			"Layer7",
						RunningAttacks: api.Running(),
						Load:           api.Load(),
					})
				 }
				}
				return servs
			}()
			functions.WriteJson(w, d)
		} else {
			w.Write([]byte("404 page not found"))
			w.WriteHeader(404)
		}
	}))
}
