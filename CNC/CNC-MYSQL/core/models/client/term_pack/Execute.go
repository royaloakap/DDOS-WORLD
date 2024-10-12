package termfx

import (
	"fmt"
	"io"
	"strings"

	"github.com/valyala/fasttemplate"
)

//ExecuteString will parse and run a termfx file/input and return the result as a string
func (r *Registry) ExecuteString(input string) (string, error) {

	t, err := fasttemplate.NewTemplate(input, r.split[0], r.split[1])
	if err != nil {
		return "", err
	}

	return t.ExecuteFuncStringWithErr(func(w io.Writer, tag string) (int, error) {

		tag = strings.ToLower(tag)
		var cmdArgs string
		el := strings.Split(tag, "(")
		for _, element := range el {
			cmdArgs = strings.Split(element, ")")[0]
		}

		if len(el) > 1 {
			el[0] += "()"
		}

		command, ok := r.commands[el[0]]
		if ok == false {
			return w.Write([]byte(fmt.Sprintf("[#Unknown tag %q#]", el[0])))
		}

		return command(w, cmdArgs)
	})

}

//Execute will parse and run a termfx file/input
func (r *Registry) Execute(input string, writer io.Writer) error {

	t, err := fasttemplate.NewTemplate(input, r.split[0], r.split[1])
	if err != nil {
		return err
	}

	_, err = t.ExecuteFunc(writer, func(w io.Writer, tag string) (int, error) {

		var cmdArgs string
		el := strings.Split(tag, "(")
		for _, element := range el {
			cmdArgs = strings.Split(element, ")")[0]
		}

		if len(el) > 1 {
			el[0] += "()"
		}

		command, ok := r.commands[el[0]]
		if ok == false {
			return w.Write([]byte(fmt.Sprintf("[#Unknown tag %q#]", el[0])))
		}

		return command(w, cmdArgs)
	})

	return err

}