package utils

import (
	"fmt"
	"time"
)

// SpinnerChars defines the set of characters for the spinner animation
var SpinnerChars = []string{"|", "/", "-", "\\"}

// StartSpinner starts the spinner animation and returns a channel to stop it
func StartSpinner() chan struct{} {
	stop := make(chan struct{})

	go func() {
		for {
			select {
			case <-stop:
				return
			default:
				for _, char := range SpinnerChars {
					fmt.Printf("\r%s", char) // Print the current spinner character
					time.Sleep(50 * time.Millisecond)
				}
			}
		}
	}()

	return stop
}