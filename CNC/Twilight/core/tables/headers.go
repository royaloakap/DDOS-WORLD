package eztable

import "strings"

// renderHeaders will render the table headers into the specified output value.
func (t *Table) renderHeaders() {
	var out []string

	if t.style.Header.HasTop() {
		out = append(out, *t.style.Header.TopLeftCorner)
		for i, cell := range t.Cells {
			out = append(out, Split(strings.Repeat(*t.style.Header.TopRow, Length(ApplyPadding(Repeat(" ", cell.FindSpacing()), cell.FindSpacing(), cell.Alignment.Header))))...)

			if i+1 == len(t.Cells) {
				continue
			} else {
				out = append(out, *t.style.Header.TopIntersection)
			}
		}
		out = append(out, *t.style.Header.TopRightCorner)
		t.output = append(t.output, out)
	}
	out = make([]string, 0)

	out = append(out, t.style.Header.HeaderLeft)
	for i, cell := range t.Cells {
		out = append(out, Split(ApplyPadding(cell.Title, cell.FindSpacing(), cell.Alignment.Header))...)

		if i+1 == len(t.Cells) {
			continue
		} else {
			out = append(out, t.style.Header.HeaderSplitter) // Write the splitter for a new header intersection.
		}
	}
	out = append(out, t.style.Header.HeaderRight)
	t.output = append(t.output, out)
	out = make([]string, 0)

	if t.style.Header.HasBottom() {
		out = append(out, *t.style.Header.BottomLeft)
		for i, cell := range t.Cells {
			out = append(out, Split(strings.Repeat(*t.style.Header.BottomRow, Length(ApplyPadding(Repeat(" ", cell.FindSpacing()), cell.FindSpacing(), cell.Alignment.Header))))...)

			if i+1 == len(t.Cells) {
				continue
			} else {
				out = append(out, *t.style.Header.BottomIntersection)
			}
		}
		out = append(out, *t.style.Header.BottomRight)
		t.output = append(t.output, out)
	}

}
