package build

import (
	"encoding/json"
	"io/ioutil"
	"os"

	"triton-cnc/core/models/json/meta"
	"triton-cnc/core/models/versions"
)

var PlanPresets *meta.PresetStart

// config file parses from struct
func NewParse_Preset_json() (error) {
	File, error := os.Open(versions.GOOS_Edition.Make["PlanPresets"])
	if error != nil {
		return error
	}

	defer File.Close()

	ByteVal, error := ioutil.ReadAll(File)
	if error != nil {
		return error
	}

	var NewConfigAllo meta.PresetStart
	error = json.Unmarshal(ByteVal, &NewConfigAllo)
	if error != nil {
		return error
	}

	PlanPresets = &NewConfigAllo
	

	return nil
}