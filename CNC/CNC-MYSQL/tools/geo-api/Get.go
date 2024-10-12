package GeoAPI

import (
	"encoding/json"
	"io/ioutil"
	"net/http"
	"net/url"
)


func Reach(Target string, Token string) (*API_Resp, error) {


	Resp, error := http.Get("http://ipinfo.io/"+url.QueryEscape(Target)+"?token="+url.QueryEscape(Token))
	if error != nil {
		return nil, error
	}

	if Resp.StatusCode != 200 {
		return nil, ErrHTTP
	}

	Read, error := ioutil.ReadAll(Resp.Body)
	if error != nil {
		return nil, ErrJsonUnmarshal
	}


	var NewRes API_Resp
	error = json.Unmarshal(Read, &NewRes)
	if error != nil {
		return nil, ErrJsonUnmarshal
	}

	return &NewRes, nil
}