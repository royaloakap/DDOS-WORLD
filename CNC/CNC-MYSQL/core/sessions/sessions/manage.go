package sessions

import "strings"

func Online() int {
	return len(SessionMap)
}


func UserSessions(user string) int {
	var Online int = 0

	for _, session := range SessionMap {
		if strings.EqualFold(user, session.User.Username) {
			Online++
			continue
		}
	}

	return Online
}

func Broadcast(Payload []byte) []string {
	var Broadcasted_Clients []string = []string{""}

	for _, session := range SessionMap {
		_, error := session.Channel.Write(Payload)
		if error != nil {
			continue
		}

		Broadcasted_Clients = append(Broadcasted_Clients, session.User.Username)
	}
	return Broadcasted_Clients
}

func Auto_Remove(session *Session_Store) {

	error := session.Conn.Wait()
	if error != nil {
		delete(SessionMap, session.Int_ID)
		return
	}

	delete(SessionMap, session.Int_ID)
	return
}