package utils

import (
	"io"
	"net/http"
)

func GetOwnIP() (string, error) {
	resp, err := http.Get("http://checkip.amazonaws.com")
	if err != nil {
		return "", err
	}
	defer resp.Body.Close()

	ip, err := io.ReadAll(resp.Body)
	if err != nil {
		return "", err
	}

	return string(ip[:len(ip)-1]), nil
}
