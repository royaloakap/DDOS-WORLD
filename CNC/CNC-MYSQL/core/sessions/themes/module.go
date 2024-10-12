package themes

import (
	"triton-cnc/core/models/json/build"
	"triton-cnc/core/sessions/sessions"
)

func CheckTheme(name string) bool {
	for I := 0; I < len(build.Themes.Themes); I++ {

		if build.Themes.Themes[I].Name == name {
			return true
		}
	}

	return false
}

func ChangeTheme(name string, session *sessions.Session_Store) bool {
	for I := 0; I < len(build.Themes.Themes); I++ {
		if name == build.Themes.Themes[I].Name {
			session.CurrentTheme = &build.Themes.Themes[I]
			return true
		}
	}

	return false
}