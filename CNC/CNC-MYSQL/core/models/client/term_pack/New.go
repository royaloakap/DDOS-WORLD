package termfx

import (
	"io"
	"sync"

	"github.com/pkg/errors"
)

var (
	//ErrFunctionAlreadyRegistered is returned after a command name conflict of a function
	ErrFunctionAlreadyRegistered = errors.New("function with the same name has already been registered")
)

//RegistryFunction is used to register a function e.g. func(name: "exit", func) can be called as exit()
type RegistryFunction func(session io.Writer, args string) (int, error)

//Registry contains all the registed commands and variables
type Registry struct {
	commands map[string]RegistryFunction

	split []string

	mutex sync.Mutex
}

//New creates a new registry which can be used to register event call backs
func New(split ...string) *Registry {

	var CustomSplit = []string{"<<", ">>"}
	if len(split) > 1 {
		CustomSplit[0] = split[0]
		CustomSplit[1] = split[1]
	}

	return &Registry{
		commands: make(map[string]RegistryFunction),
		split:    CustomSplit,
	}
}

//RegisterFunction will add the function to the registry
func (r *Registry) RegisterFunction(name string, function RegistryFunction) error {

	name += "()"

	r.mutex.Lock()
	defer r.mutex.Unlock()

	_, ok := r.commands[name]
	if ok == true {
		return ErrFunctionAlreadyRegistered
	}

	r.commands[name] = function

	return nil
}

//RegisterVariable registers a variable
func (r *Registry) RegisterVariable(name string, value string) error {

	name = "$" + name

	r.mutex.Lock()
	defer r.mutex.Unlock()

	_, ok := r.commands[name]
	if ok == true {
		return ErrFunctionAlreadyRegistered
	}

	r.commands[name] = func(session io.Writer, args string) (int, error) {
		return io.WriteString(session, value)
	}

	return nil
}