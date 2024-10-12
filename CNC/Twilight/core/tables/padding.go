package eztable

import "fmt"

// Alignment contains the alignment type.
type Alignment int

// Contain the avaliable padding items for eztable.
const (
	Centre Alignment = iota
	Left
	Right
)

// ApplyPadding will apply the desired padding item to the text provided.
func ApplyPadding(t string, s int, a Alignment) string {
	switch a {
	case Left:
		return fmt.Sprintf(" %s%s ", t, Repeat(" ", (s-Length(t))))
	case Right:
		return fmt.Sprintf(" %s%s ", Repeat(" ", (s-Length(t))), t)
	case Centre:
		return fmt.Sprintf(" %s%s%s ", Repeat(" ", (s-Length(t))/2), t, Repeat(" ", (s-Length(t))-(s-Length(t))/2))
	default:
		return fmt.Sprintf(" %s%s ", t, Repeat(" ", (s-Length(t))))
	}
}
