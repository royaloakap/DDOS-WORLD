package build

import (
	"encoding/json"
	"io/ioutil"
	"os"

	"triton-cnc/core/models/json/meta"
	"triton-cnc/core/models/versions"
)

var Config *meta.ConfigMeta

// config file parses from struct
func NewParse_config_json() (error) {
	File, error := os.Open(versions.GOOS_Edition.Make["ConfigFile"])
	if error != nil {
		return error
	}

	defer File.Close()

	ByteVal, error := ioutil.ReadAll(File)
	if error != nil {
		return error
	}

	var NewConfigAllo meta.ConfigMeta
	error = json.Unmarshal(ByteVal, &NewConfigAllo)
	if error != nil {
		return error
	}

	Config = &NewConfigAllo
	

	return nil
}