package attack_launch

import (
	"io/ioutil"
	"log"
	"net/http"
	"strconv"
	"time"
)


func ParseLaunch(attack_token string) bool {
	var Attack = &http.Client{
		Timeout: 10 * time.Second,
	}

	resp, error := Attack.Get(attack_token)
	if error != nil {
		log.Println("=====[ Attack failed ]=====")
		log.Println("ERROR: "+error.Error())
		log.Println("URL: "+attack_token)
		return false
	}

	if resp.StatusCode != 200 {
		Body, error := ioutil.ReadAll(resp.Body)
		if error != nil {
			log.Println("=====[ Attack failed ]=====")
			log.Println("HTTP CODE: "+strconv.Itoa(resp.StatusCode))
			log.Println("URL: "+attack_token)
			return false
		} else {
			log.Println("=====[ Attack failed ]=====")
			log.Println("HTTP CODE: "+strconv.Itoa(resp.StatusCode))
			log.Println("URL: "+attack_token)
			log.Println("RESP: "+string(Body))
		}
		return false
	}

	return true
}