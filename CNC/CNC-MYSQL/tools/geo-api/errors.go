package GeoAPI

import "errors"

var (
	ErrHTTP = errors.New("api has returned a http code which isnt vaild 200")

	ErrJsonUnmarshal = errors.New("api has returned a unknown response")
)