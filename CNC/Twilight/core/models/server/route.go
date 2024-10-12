package server

import (
	"net/http"
)

type RouteFunc func(http.ResponseWriter, *http.Request)

type Route struct {
	Name      string
	Handler   RouteFunc
	Subrouter bool
	Subroutes []*Route
}

func NewRoute(name string, handler RouteFunc) *Route {
	return &Route{
		Name:      name,
		Handler:   handler,
		Subrouter: false,
		Subroutes: make([]*Route, 0),
	}
}

func NewSubRouter(name string) *Route {
	return &Route{
		Name:      name,
		Handler:   nil,
		Subrouter: true,
		Subroutes: make([]*Route, 0),
	}
}

func (router *Route) NewSub(route *Route) {
	router.Subroutes = append(router.Subroutes, route)
}

func (router *Route) NewSubs(routes ...*Route) {
	router.Subroutes = append(router.Subroutes, routes...)
}
