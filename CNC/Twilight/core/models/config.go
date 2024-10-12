package models

var (
	Config *Conf = new(Conf)
)

type Conf struct {
	Name    string `json:"name"`
	Domain  string `json:"domain"`
	Secure  bool   `json:"secure"`
	Cert    string `json:"cert"`
	Vers    string `json:"version"`
	Key     string `json:"key"`
	Autobuy struct {
		Key string `json:"key"`
		Email string `json:"email"`
	} `json:"autobuy"`
	Fake struct {
		Users 	   int 	 `json:"users"`
		Attacks    int   `json:"attacks"`
	} `json:"fake"`
	Server struct {
		Enabled bool `json:"enabled"`
		SSH    string   `json:"ssh"`
		Telnet string	`json:"telnet"`
	} `json:"cnc"`
}
