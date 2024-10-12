package external

import "sync"

var (
	Command = make(map[string]*Storage)
	Mutex sync.Mutex
)

type Storage struct {
	Name string
	Description string
	Admin bool
	Reseller bool
	VIP bool
	
	Banner []string
}
