package panelapi

import "api/core/models/server"

var (
	Route *server.Route = server.NewSubRouter("/attacks")
)
