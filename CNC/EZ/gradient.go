package main

import (
	"strconv"
)

func Generate(startrgb []int, endrgb []int, text string) string {

	var changer = int((int(endrgb[0]) - int(startrgb[0])) / len(text))
	var changeg = int((int(endrgb[1]) - int(startrgb[1])) / len(text))
	var changeb = int((int(endrgb[2]) - int(startrgb[2])) / len(text))

	var finalText string

	var r = startrgb[0]
	var g = startrgb[1]
	var b = startrgb[2]

	for i := 0; i < len(text); i++ {
		finalText += "\033[38;2;" + strconv.Itoa(r) + ";" + strconv.Itoa(g) + ";" + strconv.Itoa(b) + "m" + string(text[i])
		r += changer
		g += changeg
		b += changeb
	}
	return finalText

}
