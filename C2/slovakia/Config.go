package main

import (
	"os"
	"path/filepath"

	"github.com/naoina/toml"
)

var (
	// Version control for the application
	Version string = "v2.0"

	Options *Config = new(Config)

	// Holds the banner which is used on connection for slaves
	// Make sure to keep the Banner block between a certain size (Suggested 4)
	Banner []byte = []byte{0x00, 0x00, 0x00, 0x01}

	// By default attacks are enabled.
	// Can be changed inside the configs
	Attacks bool = true
)

// Configuration structure for the server.toml file
type Config struct {
	Templates struct {
		Server struct {
			Protocol string `toml:"protocol"`
			Listener string `toml:"listener"`
		} `toml:"server"`
		Slaves struct {
			Protocol string `toml:"protocol"`
			Listener string `toml:"listener"`
		} `toml:"slaves"`
		Database struct {
			Local    string `toml:"local"`
			Defaults struct {
				Maxtime     int  `toml:"maxtime"`
				Admin       bool `toml:"admin"`
				API         bool `toml:"api"`
				Cooldown    int  `toml:"cooldown"`
				MaxDaily    int  `toml:"max_daily"`
				Concurrents int  `toml:"concurrents"`
			} `toml:"defaults"`
		} `toml:"database"`
		API struct {
			Listener string `toml:"listener"`
			TLS      bool   `toml:"tls"`
			Key      string `toml:"key"`
			Cert     string `toml:"cert"`
		} `toml:"api"`
		Attacks struct {
			MaximumOngoing int `toml:"maximum_ongoing"`
		} `toml:"attacks"`
	} `toml:"templates"`
}

// OpenConfig will open a config and parses with the interface{} field
func OpenConfig(v interface{}, f ...string) error {
	config, err := os.Open(filepath.Join(f...))
	if err != nil {
		return err
	}

	return toml.NewDecoder(config).Decode(v)
}