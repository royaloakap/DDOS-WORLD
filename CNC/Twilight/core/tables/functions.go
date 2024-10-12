package eztable

import (
	"regexp"
	"strings"

	"github.com/mattn/go-runewidth"
)

// Length will find the true length of a provided string.
func Length(v string) int {
	return runewidth.StringWidth(strings.ReplaceAll(regexp.MustCompile("[\u001B\u009B\x1b][[\\]()#;?]*(?:(?:(?:[a-zA-Z\\d]*(?:;[a-zA-Z\\d]*)*)?\u0007)|(?:(?:\\d{1,4}(?:;\\d{0,4})*)?[\\dA-PRZcf-ntqry=><~]))").ReplaceAllString(v, ""), "<escape>", ""))
}

// Strip will strip an item of text of all escape codes entirley.
func Strip(v string) string {
	return regexp.MustCompile("[\u001B\u009B\x1b][[\\]()#;?]*(?:(?:(?:[a-zA-Z\\d]*(?:;[a-zA-Z\\d]*)*)?\u0007)|(?:(?:\\d{1,4}(?:;\\d{0,4})*)?[\\dA-PRZcf-ntqry=><~]))").ReplaceAllString(v, "")
}

func (c *Cell) ColorizeCell(hexCode string) *Cell {
    c.Title = "\x1b[38;2;" + hexCode + "m" + c.Title + "\x1b[0m"
    for i, body := range c.Bodys {
        c.Bodys[i] = "\x1b[38;2;" + hexCode + "m" + body + "\x1b[0m"
    }
    return c
}

func (t *Table) ColorizeTable(hexCode string) *Table {
    for _, c := range t.Cells {
        c.ColorizeCell(hexCode)
    }
    return t
}

// Repeat will repeat a specified string with the provided amount of loops.
func Repeat(s string, t int) string {
	var out string

	// We use this function instead of the actual string repeater because if a negative number is provided, it will not crash.
	for i := 0; i < t; i++ {
		out += s
	}

	return out
}

// TotalBodys will figure out how many body values we actually have.
func (t *Table) TotalBodys() int {
	var count int = 0

	for _, c := range t.Cells {
		if len(c.Bodys) > count {
			count = len(c.Bodys)
		}
	}

	return count
}

// Split will split the text character by character, except escape codes which are stored as a whole item in itself.
func Split(i string) []string {
	// Regular expression to match ANSI escape codes
	regex := `\x1b\[([0-9]{1,2}(;[0-9]{1,2})*)?[m|K]`

	// Compile the regular expression
	re := regexp.MustCompile(regex)

	// Find all occurrences of escape codes
	matches := re.FindAllStringIndex(i, -1)

	// Initialize the resulting slice with a capacity for efficiency
	var result []string
	result = make([]string, 0, len(matches)+1)

	// Add substrings between escape codes to the result slice
	startIdx := 0
	for _, matchIdx := range matches {
		endIdx := matchIdx[0]
		escapeCode := matchIdx[1]

		// Append regular characters between escape codes
		result = append(result, i[startIdx:endIdx])

		// Append the escape code as a single element
		result = append(result, i[endIdx:escapeCode])

		// Move the startIdx to the next position
		startIdx = escapeCode
	}

	// Append the remaining characters after the last escape code (or the entire string if no escape codes are found)
	result = append(result, i[startIdx:])

	return result
}

// FindSpacing will find the current spacing of a cell.
func FindSpacing(c *Cell) int {
	var Spacing int = 0

	for _, b := range c.Bodys {
		if Length(b) > Spacing {
			Spacing = Length(b)
		}
	}

	if Length(c.Title) > Spacing {
		Spacing = Length(c.Title)
	}

	return Spacing
}
