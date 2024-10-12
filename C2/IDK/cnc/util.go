package cnc

import (
	"fmt"
	"strings"
)

func netshift(prefix uint32, netmask uint8) uint32 {
	return uint32(prefix >> (32 - netmask))
}

func Fade(startcolor, endcolor []int, line string) string {
	var retstr string
	var splitted []string
	strsplit := strings.Split(line, "\n")
	for _, str := range strsplit {
		splitted = append(splitted, str)
	}
	for i, splitstr := range splitted {
		if len(splitstr) == 0 {
			continue
		}
		var changer int
		var changeg int
		var changeb int
		var r, g, b int
		changer = int((int(endcolor[0]) - int(startcolor[0])) / len(splitstr))
		changeg = int((int(endcolor[1]) - int(startcolor[1])) / len(splitstr))
		changeb = int((int(endcolor[2]) - int(startcolor[2])) / len(splitstr))
		r = int(startcolor[0])
		g = int(startcolor[1])
		b = int(startcolor[2])
		for _, letter := range splitstr {
			retstr = retstr + "\x1b[38;2;" + fmt.Sprint(r) + ";" + fmt.Sprint(g) + ";" + fmt.Sprint(b) + "m" + string(letter) + ""
			r += changer
			g += changeg
			b += changeb
		}
		if len(splitted) > 1 {
			i++
		}
		if i > 0 {
			retstr += "\r\n"
		}
	}
	return retstr + "\033[38;5;" + fmt.Sprint(endcolor[0]) + ";" + fmt.Sprint(endcolor[1]) + ";" + fmt.Sprint(endcolor[2]) + "m"
}

func Fadenonewln(startcolor, endcolor []int, line string) string {
	var retstr string
	var splitted []string
	strsplit := strings.Split(line, "\n")
	for _, str := range strsplit {
		splitted = append(splitted, str)
	}
	for i, splitstr := range splitted {
		if len(splitstr) == 0 {
			continue
		}
		var changer int
		var changeg int
		var changeb int
		var r, g, b int
		changer = int((int(endcolor[0]) - int(startcolor[0])) / len(splitstr))
		changeg = int((int(endcolor[1]) - int(startcolor[1])) / len(splitstr))
		changeb = int((int(endcolor[2]) - int(startcolor[2])) / len(splitstr))
		r = int(startcolor[0])
		g = int(startcolor[1])
		b = int(startcolor[2])
		for _, letter := range splitstr {
			retstr = retstr + "\x1b[38;2;" + fmt.Sprint(r) + ";" + fmt.Sprint(g) + ";" + fmt.Sprint(b) + "m" + string(letter) + ""
			r += changer
			g += changeg
			b += changeb
		}
		if len(splitted) > 1 {
			i++
		}
	}
	return retstr + "\033[38;5;" + fmt.Sprint(endcolor[0]) + ";" + fmt.Sprint(endcolor[1]) + ";" + fmt.Sprint(endcolor[2]) + "m"
}
