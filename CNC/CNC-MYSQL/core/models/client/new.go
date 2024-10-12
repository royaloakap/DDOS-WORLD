package client

import "sync"

var (
	ClientMap  = make(map[string]string)
	MuxSyncCSM sync.Mutex
)