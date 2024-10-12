package HandleFunc

import (
	"regexp"
	"strings"


	"golang.org/x/crypto/ssh"
)


type New struct {
	Username string
	Channel ssh.Channel
}



func (r *New) CallBackModify(line string, pos int, key rune) (newLine string, newPos int, ok bool) {


	if key != '\t' || pos != len(line) {
		return
	}
	lastWord := regexp.MustCompile(`.+\W(\w+)$`)
	if !strings.Contains(line, " ") {
		var name string
		return name, len(name), true
	}

	if strings.HasSuffix(line, " ") {
		return line, pos, true
	}
	m := lastWord.FindStringSubmatch(line)
	if m == nil {
		return line, len(line), true
	}
	soFar := m[1]
	var match []string

	if len(match) == 0 {
		return
	}
	if len(match) > 1 {
		return line, pos, true
	}
	newLine = line[:len(line)-len(soFar)] + match[0]
	return newLine, len(newLine), true
}
