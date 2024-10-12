package adminapi

import "api/core/models/server"

var (
	Route *server.Route = server.NewSubRouter("/admin")
)
