// Package main contains the executable main.
package main

import (
	"fmt"
	"log"
	"time"

	"github.com/borud/tun/pkg/global"
	"github.com/borud/tun/pkg/tun"
	"github.com/borud/tun/pkg/util"
)

var opt struct {
	KeyFile          string        `long:"key" description:"SSH key file"`
	Target           string        `long:"target" default:"localhost:22" description:"target SSH server for login on local end" required:"yes"`
	RemoteListenAddr string        `long:"remote-listen-addr" default:"localhost:2222" description:"remote listener address" required:"yes"`
	Via              []string      `long:"via" description:"hosts we jump via on format user@host:port"`
	ReconnectWait    time.Duration `long:"reconnect" default:"30s" description:"reconnect interval"`
	Version          bool          `long:"version" description:"show version"`
}

func main() {
	util.FlagParse(&opt)

	if opt.Version {
		fmt.Println(global.Version)
		return
	}

	if opt.KeyFile == "" {
		log.Fatal("please specify --key")
	}

	if len(opt.Via) == 0 {
		log.Fatal("please specify --via")
	}

	t, err := tun.New(tun.Config{
		KeyFile:          opt.KeyFile,
		Target:           opt.Target,
		RemoteListenAddr: opt.RemoteListenAddr,
		Chain:            opt.Via,
	})
	if err != nil {
		log.Fatal(err)
	}

	for {
		err = t.Run()
		if err != nil {
			log.Printf("terminated: %v", err)
		}
		log.Printf("waiting %s before attempting new connection", opt.ReconnectWait)
		time.Sleep(opt.ReconnectWait)
	}
}
