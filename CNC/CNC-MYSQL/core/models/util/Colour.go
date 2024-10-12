package util


func Colour(boolen bool, Colour bool) string {
	if boolen {
		if Colour {
			return "\x1b[38;5;2mtrue\x1b[0m"
		} else {
			return "true"
		}
	} else {
		if Colour {
			return "\x1b[38;5;1mfalse\x1b[0m"
		}

		return "false"
	}
}