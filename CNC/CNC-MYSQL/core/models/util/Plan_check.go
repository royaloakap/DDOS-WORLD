package util

import "time"

func Plan_expiry(End int64) bool {
	if End > time.Now().Unix() {
		return true
	}

	return false
}