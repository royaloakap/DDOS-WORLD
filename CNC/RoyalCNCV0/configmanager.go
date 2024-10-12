package main

import (
	"os"
	"strings"
)

func getConfig() map[string]string {
	configFile := "./config.royal"
	data := make(map[string]string)
	file, err := os.ReadFile(configFile)
	if err != nil {
		return data
	}
	fileContent := string(file)
	for _, line := range strings.Split(fileContent, "\r\n") {
		if strings.Contains(line, "=") {
			split := strings.Split(line, "=")
			data[split[0]] = split[1]
		}
	}
	return data
}

func config(key string) string {
	config := getConfig()
	return config[key]
}
