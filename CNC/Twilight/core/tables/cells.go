package eztable

// Cell contains the data for a table cell.
type Cell struct {
	Alignment *Align
	Title     string
	Bodys     []string
}

// Align contains the allignment data for a cell.
type Align struct {
	Header Alignment
	Body   Alignment
}

// FindSpacing will find the spacing for a cell provided.
func (c *Cell) FindSpacing() int {
	var Spacing int = 0

	for _, b := range c.Bodys {
		if Length(b) > Spacing {
			Spacing = Length(b)
		}
	}

	// Check if the length of the bodys is less than the length of the title.
	if Length(c.Title) > Spacing {
		Spacing = Length(c.Title)
	}

	return Spacing
}
