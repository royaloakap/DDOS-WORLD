package main

import (
	"encoding/json"
	"os"
)

var methods map[string]string

func LoadMethods() {
	file, err := os.Open("methods.json")
	if err != nil {
		logger.Fatal(err)
	}
	json.NewDecoder(file).Decode(&methods)
	logger.Println(len(methods), "registered methods!")
}
