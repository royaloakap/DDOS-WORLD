package eztable

import (
	"fmt"
	"strings"
)

// Table contains the table information.
type Table struct {
	style  *Style
	Cells  []*Cell
	output [][]string
}

// SetStyle will set the tables style.
func (t *Table) SetStyle(s *Style) error {
	if s.Header == nil {
		return fmt.Errorf("no body styling inside of style")
	}
	if s.Body == nil {
		return fmt.Errorf("no header styling inside of style")
	}

	t.style = s
	return nil
}

// String will convert the table to a string.
func (t *Table) String() (o string) {
	t.renderHeaders()
	t.renderBody()

	for _, c := range t.output {
		o += strings.Join(c, "") + "\r\n"
	}

	return strings.TrimSuffix(o, "\r\n")
}

// Actual will return the table in a 2D matrix array.
func (t *Table) Actual() [][]string {
	t.renderHeaders()
	t.renderBody()

	return t.output
}

// NewTable will create a new table structure.
func NewTable() *Table {
	return new(Table)
}
