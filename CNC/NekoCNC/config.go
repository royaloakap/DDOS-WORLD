package main

import (
	"github.com/tidwall/gjson"
	"io/ioutil"
	"fmt"
)

func loadconfig(checkfor  string) string {
	redconfig, err := ioutil.ReadFile("config.json")
    if err != nil {
        fmt.Println("ERROR\nconfig.json not found!\n")
    }
    configfil := string(redconfig)
    spitout := (gjson.Get(configfil, checkfor)).String()

    return spitout
}