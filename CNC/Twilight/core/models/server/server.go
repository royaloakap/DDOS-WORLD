package server

import (
	"api/core/models"
	"api/core/models/antiflood"
	"fmt"
	"log"
	"net/http"
	"os"
	"strings"
	"time"
	"errors"
	"github.com/gorilla/mux"
)

type Server struct {
	mux    http.Server
	router *mux.Router
	routes map[string]*Route
	logger *log.Logger
	config *Config
}

func NewServer(config *Config) *Server {
	return &Server{
		mux: http.Server{
			Addr:         config.Addr,
			Handler:      nil,
			WriteTimeout: 15 * time.Second,
			ReadTimeout:  15 * time.Second,
		},
		router: mux.NewRouter(),
		routes: make(map[string]*Route),
		logger: log.New(os.Stderr, "[server] ", log.Ltime|log.Lshortfile),
		config: config,
	}
}
func (s *Server) ListenAndServe() error {
    s.router.Use(antiflood.Limit(
        5,
        5*time.Second,
        antiflood.WithKeyFuncs(antiflood.KeyByRealIP, antiflood.KeyByEndpoint),
        antiflood.WithLimitHandler(func(w http.ResponseWriter, r *http.Request) {
            // Rate limiting handler
            w.Header().Set("Content-Type", "application/json")
            w.WriteHeader(http.StatusTooManyRequests)
            w.Write([]byte(`{"status": "error", "message":"you have been ratelimited!"}`))
        }),
    ))
    
    s.router.Use(antiflood.Limit(
        750,
        1*time.Minute,
        antiflood.WithKeyFuncs(antiflood.KeyByRealIP),
        antiflood.WithLimitHandler(func(w http.ResponseWriter, r *http.Request) {
            // Rate limiting handler
            w.Header().Set("Content-Type", "application/json")
            w.WriteHeader(http.StatusTooManyRequests)
            w.Write([]byte(`{"status": "error", "message":"you have been ratelimited!"}`))
        }),
    ))
    
    s.mux.Handler = s.router
    
	if models.Config.Secure {
		cert := models.Config.Cert
		key := models.Config.Key
		if cert == "" || key == "" {
			return errors.New("certificate or key is empty")
		}
		s.mux.Addr = strings.Split(s.config.Addr, ":")[0] + ":443"
		log.Print("Server is running on HTTPS on " + s.mux.Addr)
		return s.mux.ListenAndServeTLS(cert, key)
	} else {
		log.Print("Server is running on HTTP on " + s.mux.Addr)
	}
    
    s.logger.Println("listening with " + fmt.Sprint(s.Subrouters()) + " subrouters and " + fmt.Sprint(s.Routes()) + " routes.")
    
    return s.mux.ListenAndServe()
}

func (s *Server) Subrouters() int {
	subs := 0
	for _, sub := range s.routes {
		if sub.Subrouter {
			subs++
		}
	}
	return subs
}
func (s *Server) Routes() int {
	subs := 0
	for _, sub := range s.routes {
		if !sub.Subrouter && sub.Handler != nil {
			subs++
		}
	}
	return subs
}
