package attack_launch

import (
	"net/url"
	"strings"
	"triton-cnc/core/models/middleware/attack_sort"
)

type Attack struct {
	Target string
	Port string
	Duration string
	Method string
}

func Parse(method string, attacking *Attack) string {
	if attacksort.Methods_Map[method].UrlEncode {
		return EncodeURL(attacksort.Methods_Map[method].Target_API, attacking)
	} else {
		return URLParse(attacksort.Methods_Map[method].Target_API, attacking)
	}
}

func URLParse(c string, New *Attack) string {
	c = strings.Replace(c, "[target]", New.Target, -1)
	c = strings.Replace(c, "[port]", New.Port, -1)
	c = strings.Replace(c, "[duration]", New.Duration, -1)
	c = strings.Replace(c, "[method]", New.Method, -1)
	return c
}

func EncodeURL(c string, New *Attack) string {
	c = strings.Replace(c, "[target]", url.QueryEscape(New.Target), -1)
	c = strings.Replace(c, "[port]", url.QueryEscape(New.Port), -1)
	c = strings.Replace(c, "[duration]", url.QueryEscape(New.Duration), -1)
	c = strings.Replace(c, "[method]", url.QueryEscape(New.Method), -1)
	return c
}