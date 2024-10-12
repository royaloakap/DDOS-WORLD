// Package util contains things that don't fit anywhere else.
package util

import (
	"fmt"
	"os"

	"github.com/jessevdk/go-flags"
)

// FlagParse is a wrapper for the flag parsing in jessevdk/go-flags which is a bit awkward.
func FlagParse(options any) {
	p := flags.NewParser(options, flags.Default)
	if _, err := p.Parse(); err != nil {
		if flagsErr, ok := err.(*flags.Error); ok {
			switch flagsErr.Type {
			case flags.ErrHelp:
				os.Exit(0)

			case flags.ErrCommandRequired:
				os.Exit(1)

			case flags.ErrUnknownCommand:
				os.Exit(1)

			case flags.ErrRequired:
				os.Exit(1)

			case flags.ErrUnknownFlag:
				os.Exit(1)

			case flags.ErrMarshal:
				os.Exit(1)

			case flags.ErrExpectedArgument:
				os.Exit(1)

			default:
				fmt.Printf("%v [%d]\n", err, flagsErr.Type)
				os.Exit(0)
			}
		}
		os.Exit(1)
	}
}
