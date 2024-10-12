package server

func (s *Server) AddRoute(route *Route) {
	if s.routeExists(route) {
		return
	}
	s.addNamedRoute(route)
	if route.Name != "" && !route.Subrouter {
		s.router.HandleFunc(route.Name, route.Handler)
		s.logger.Println("succesfully added \"/" + route.Name + "\" route")
	}
	if len(route.Subroutes) > 0 {
		for _, sub := range route.Subroutes {
			if route.Name == "" {
				s.addSubRoute("", sub)
				continue
			}
			s.addSubRoute(route.Name, sub)
		}
	}
}

func (s *Server) addNamedRoute(route *Route) {
	s.routes[route.Name] = route
}

func (s *Server) addSubRoute(name string, route *Route) {
	if s.routeExists(route) {
		return
	}
	if route.Subrouter && route.Name != "" {
		s.logger.Println("succesfully added \"" + name + route.Name + "\" subrouter!")
	}
	s.addNamedRoute(&Route{
		Name:      name + route.Name,
		Handler:   route.Handler,
		Subroutes: route.Subroutes,
		Subrouter: route.Subrouter,
	})
	if name == "" && route.Handler != nil {
		s.router.HandleFunc(route.Name, route.Handler)
		s.logger.Println("succesfully added \"" + name + route.Name + "\" route")
	} else if route.Handler != nil {
		s.router.HandleFunc(name+route.Name, route.Handler)
		s.logger.Println("succesfully added \"" + name + route.Name + "\" route")
	}
	if len(route.Subroutes) > 0 {
		for _, sub := range route.Subroutes {
			s.addSubRoute(name+route.Name, sub)
		}
	}
}

func (s *Server) AddRoutes(routes ...*Route) {
	for _, route := range routes {
		s.AddRoute(route)
	}
}

func (s *Server) routeExists(route *Route) bool {
	if router, ok := s.routes[route.Name]; ok {
		if router.Handler == nil && route.Handler != nil {
			router.Handler = route.Handler
			s.router.HandleFunc(route.Name, route.Handler)
			return false
		} else if router.Handler != nil {
			return true
		}
	}
	return false
}
