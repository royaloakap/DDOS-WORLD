package api

import "net/http"

func NotAllowed(w http.ResponseWriter, r *http.Request) {
	w.Write([]byte("403 nigga gtfo."))
}
