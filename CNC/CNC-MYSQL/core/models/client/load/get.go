package load

import (
	"io/ioutil"
	"strings"

	"triton-cnc/core/models/client"
	"triton-cnc/core/models/versions"
)


func Load() (int, error) {

	WalkedDir, error := ioutil.ReadDir(versions.GOOS_Edition.Make["BrandingFolder"])
	if error != nil {
		return 0, error
	}

	var Loaded int = 0

	for _, File := range WalkedDir {
		

		Name := strings.Split(File.Name(), ".")
		if len(Name) < 1 {
			continue
		}

		Containing, error := ioutil.ReadFile(versions.GOOS_Edition.Make["BrandingFolder"]+"/"+File.Name())
		if error != nil {
			continue
		}

		client.MuxSyncCSM.Lock()
		client.ClientMap[Name[0]] = string(Containing)
		client.MuxSyncCSM.Unlock()

		Loaded++

		continue
	}

	return Loaded, nil
}