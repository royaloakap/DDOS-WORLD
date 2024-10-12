package floods

import "time"

type Attack struct {
	ID     int
	Target string
	*Method
	Port, Threads, PPS, Subnet int
	Parent, Stopped            int
	Duration                   int
	Created                    int64
}

func New(method string) *Attack {
	if ok := Get(method); ok != nil {
		return &Attack{
			Target:   "",
			Duration: 0,
			Port:     0,
			Threads:  3,
			PPS:      250000,
			Method:   ok,
			Created:  time.Now().Unix(),
			Parent:   0,
		}
	}
	return nil
}
