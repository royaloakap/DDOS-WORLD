package build

import (
	"encoding/json"
	"io/ioutil"
	"os"

	"triton-cnc/core/models/json/meta"
	"triton-cnc/core/models/versions"
)

var Themes *meta.Themes

// config file parses from struct
func NewParse_Themes_json() (error) {
	File, error := os.Open(versions.GOOS_Edition.Make["Themes"])
	if error != nil {
		return error
	}

	defer File.Close()

	ByteVal, error := ioutil.ReadAll(File)
	if error != nil {
		return error
	}

	var NewConfigAllo meta.Themes
	error = json.Unmarshal(ByteVal, &NewConfigAllo)
	if error != nil {
		return error
	}

	Themes = &NewConfigAllo
	

	return nil
}