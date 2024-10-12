package main

import (
	"fmt"
	"log"
)

func main() {
	fmt.Printf("Slovakia %s\r\n", Version)
	fmt.Printf("\033[107m                        \033[0m\r\n")
	fmt.Printf("\033[107m                        \033[0m\r\n")
	fmt.Printf("\033[101m                        \033[0m\r\n")
	fmt.Printf("\033[101m                        \033[0m\r\n")
	fmt.Printf("\033[104m                        \033[0m\r\n")
	fmt.Printf("\033[104m                        \033[0m\r\n")

	if err := OpenConfig(Options, "templates", "server.toml"); err != nil {
		log.Fatalf("Config: %v", err)
	}

	if err := SpawnSQL(); err != nil {
		log.Fatalf("Config: %v", err)
	}


	go Master()
	go NewAPI()

	// Execute the main slave listener
	if err := Slave(); err != nil {
		log.Fatalf("Config: %v", err)
	}
}