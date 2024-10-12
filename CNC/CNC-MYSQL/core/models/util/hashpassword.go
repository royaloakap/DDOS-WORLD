package util

import (
	"crypto/sha256"
	"encoding/hex"
)


func HashPassword(password string) (string) {
	return hex.EncodeToString(sha256.New().Sum([]byte(password)))
}