package main

import (
	"encoding/json"
	"os"
)

var (
	Config = new(conf)
)

type conf struct {
	Name  string `json:"name"`
	Slots string `json:"slots"`
	Type  string `json:"type"`
}

func Load() {
	file, err := os.Open("config.json")
	if err != nil {
		logger.Fatal(err)
	}
	json.NewDecoder(file).Decode(&Config)
	logger.Println("succesfully read config! (s.name=\"" + Config.Name + "\")")
}
