package main

import (
	"encoding/json"
	"errors"
	"io/ioutil"
	"os"
)

type Method struct {
	Enabled     bool     `json:"enabled"`
	Method      string   `json:"method"`
	Group       string   `json:"group"`
	DefaultPort uint16   `json:"defaultPort"`
	DefaultTime uint32   `json:"defaultTime"`
	Permission  []string `json:"permission"`
	API         []string `json:"api"`
}

func getMethodsList() []Method {
	filename := "./methods.json"
	file, err := os.Open(filename)
	if err != nil {
		return []Method{}
	}
	defer file.Close()
	data, err := ioutil.ReadAll(file)
	if err != nil {
		return []Method{}
	}
	var methods []Method
	err = json.Unmarshal(data, &methods)
	if err != nil {
		return []Method{}
	}
	return methods
}

func getMethod(method string) (Method, error) {
	for _, m := range getMethodsList() {
		if m.Method == method {
			return m, nil
		}
	}
	return Method{}, errors.New("Method not found")
}

func hasVipPermission(method string) bool {
	m, err := getMethod(method)
	if err != nil {
		return false
	}
	for _, p := range m.Permission {
		if p == "VIP" {
			return true
		}
	}
	return false
}

func hasPrivatePermission(method string) bool {
	m, err := getMethod(method)
	if err != nil {
		return false
	}
	for _, p := range m.Permission {
		if p == "PRIVATE" {
			return true
		}
	}
	return false
}

func hasAdminPermission(method string) bool {
	m, err := getMethod(method)
	if err != nil {
		return false
	}
	for _, p := range m.Permission {
		if p == "ADMIN" {
			return true
		}
	}
	return false
}

func groupMethodsByGroup(methods []Method) map[string][]Method {
	groupedMethods := make(map[string][]Method)

	for _, method := range methods {
		group := method.Group
		groupedMethods[group] = append(groupedMethods[group], method)
	}

	return groupedMethods
}
