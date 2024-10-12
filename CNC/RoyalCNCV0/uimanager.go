package main

import (
	"os"
	"strings"
)

func ui(filename string, content map[string]string) string {
	name := "./ui/" + filename + ".royal"
	file, err := os.ReadFile(name)
	if err != nil {
		return ""
	}
	fileContent := string(file)
	for key, value := range content {
		fileContent = strings.ReplaceAll(fileContent, "<<$"+key+">>", value)
	}
	return fileContent
}
