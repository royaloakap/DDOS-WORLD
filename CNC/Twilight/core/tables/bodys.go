package eztable

import "strings"

// renderBody will render each table body into the output variable.
func (t *Table) renderBody() {
	var (
		out      []string = make([]string, 0)
		Position int      = 0
	)

	for i := 0; i < t.TotalBodys(); i++ {
		out = append(out, t.style.Body.BodyLeft)
		for y := 0; y < len(t.Cells); y++ {
			cell := t.Cells[y]
			if Position > len(cell.Bodys)-1 {
				out = append(out, Split(strings.Repeat(" ", Length(ApplyPadding(Repeat(" ", cell.FindSpacing()), cell.FindSpacing(), cell.Alignment.Header))))...)
			} else {
				out = append(out, Split(ApplyPadding(cell.Bodys[Position], cell.FindSpacing(), cell.Alignment.Body))...)
			}

			if y+1 == len(t.Cells) {
				break
			} else {
				out = append(out, t.style.Body.BodySplitter)
			}
		}

		out = append(out, t.style.Body.BodyRight)
		Position++
		t.output = append(t.output, out)
		out = make([]string, 0)
	}



	if t.style.Body.HasBottom() {
		out = append(out, *t.style.Body.BottomLeftCorner)
		for i, cell := range t.Cells {
			out = append(out, Split(strings.Repeat(*t.style.Body.BottomRow, Length(ApplyPadding(Repeat(" ", cell.FindSpacing()), cell.FindSpacing(), cell.Alignment.Body))))...)

			if i+1 == len(t.Cells) {
				continue
			} else {
				out = append(out, *t.style.Body.BottomIntersection)
			}
		}
		out = append(out, *t.style.Body.BottomRightCorner)
		t.output = append(t.output, out)
	}
}
