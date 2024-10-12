package servers

import (
	"log"
	"os"
)

var (
	Servers map[string]*Server = make(map[string]*Server)
	Config  *Configuration
	logger  = log.New(os.Stderr, "[servers] ", log.Ltime|log.Lshortfile)
)

type Configuration struct {
	Listener int      `json:"listener"`
	Allowed  []string `json:"allowed"`
	Key      string   `json:"key"`
}
