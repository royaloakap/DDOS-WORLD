package api

import "net/http"

func NotFound(w http.ResponseWriter, r *http.Request) {
	w.Write([]byte("404 nigga gtfo."))
}
