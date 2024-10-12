package managerapi

import "api/core/models/server"

var (
	Route *server.Route = server.NewSubRouter("/manager")
)
