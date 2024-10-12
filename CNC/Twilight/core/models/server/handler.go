package server

import "net/http"

type Handler struct {
	Name     string
	function http.Handler
}

func NewHandler(name string, handler http.Handler) *Handler {
	return &Handler{
		Name:     name,
		function: handler,
	}
}

func (s *Server) AddHandler(handler *Handler) {
	s.router.PathPrefix(handler.Name).Handler(handler.function)
}
