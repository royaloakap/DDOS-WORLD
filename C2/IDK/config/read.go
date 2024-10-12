package config

import (
	"HellSing/HellStruct"
	"HellSing/cnc"
	"encoding/json"
	"fmt"
	"io/ioutil"
)

func ReadConfig() {
	file, err := ioutil.ReadFile("./config/config.json")
	if err != nil {
		fmt.Println("\033[38;5;241m[\033[38;2;191;0;0mAuthority\033[38;5;241m.] [" + err.Error() + "]")
	}
	err1 := json.Unmarshal(file, &HellStruct.Configuration)
	if err1 != nil {
		fmt.Println("\033[38;5;241m[\033[38;2;191;0;0mAuthority\033[38;5;241m.] [" + err1.Error() + "]")
	}
	for _, method := range HellStruct.Configuration.Methods {
		cnc.AttackInfoLookup[method.Name] = &cnc.AttackInfo{
			AttackID:          uint8(method.Type),
			AttackFlags:       method.Flags,
			AttackDescription: method.Description,
		}
	}
}
