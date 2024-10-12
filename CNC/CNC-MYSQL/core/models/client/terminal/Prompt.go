package terminal

import (
	"bufio"
	"strings"

	"triton-cnc/core/sessions/sessions"
	"triton-cnc/core/models/client"

)

func PromptBuild(Name string, session *sessions.Session_Store) ([]string, int) {

	scanner := bufio.NewScanner(strings.NewReader(client.ClientMap[Name]))

	var Line []string

	for scanner.Scan() {

		Test := strings.Replace(scanner.Text(), "<<$username>>", session.User.Username, -1)

		Test = strings.Replace(Test, "\\x1b", "", -1)
		Test = strings.Replace(Test, "\\033", "", -1)
		Line = append(Line, Test)
	}

	return Line, len(Line)
}