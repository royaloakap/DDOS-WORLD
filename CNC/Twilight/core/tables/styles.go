package eztable

// Style contains the data for a table style.
type Style struct {
	Header *Header
	Body   *Body
}

// Header contains the style data for a table header.
type Header struct {
	TopLeftCorner      *string
	TopRightCorner     *string
	TopIntersection    *string
	TopRow             *string
	HeaderLeft         string
	HeaderSplitter     string
	HeaderRight        string
	BottomLeft         *string
	BottomRight        *string
	BottomIntersection *string
	BottomRow          *string
}

// Body contains the style data for a table body.
type Body struct {
	BodyLeft           string
	BodyRight          string
	BodySplitter       string
	BottomLeftCorner   *string
	BottomRightCorner  *string
	BottomIntersection *string
	BottomRow          *string
}

// HasTop will check if a header has a top item above it.
func (h *Header) HasTop() bool {
	return h.TopLeftCorner != nil && h.TopRightCorner != nil && h.TopRow != nil && h.TopIntersection != nil
}

// HasBottom will check if a header has a top item below it.
func (h *Header) HasBottom() bool {
	return h.BottomLeft != nil && h.BottomRight != nil && h.BottomRow != nil && h.BottomIntersection != nil
}

// HasBottom will check if a body has a base item below it.
func (b *Body) HasBottom() bool {
	return b.BottomLeftCorner != nil && b.BottomRightCorner != nil && b.BottomRow != nil && b.BottomIntersection != nil
}

// ptr will convert a string to a string pointer.
func ptr(s string) *string { return &s }

var (
	Lite *Style = &Style{
		Header: &Header{
			TopLeftCorner:      ptr("♥"),
			TopRightCorner:     ptr("♥"),
			TopIntersection:    ptr("♥"),
			TopRow:             ptr("♥"),
			HeaderLeft:         "♥",
			HeaderSplitter:     "♥",
			HeaderRight:        "♥",
			BottomLeft:         ptr("♥"),
			BottomRight:        ptr("♥"),
			BottomIntersection: ptr("♥"),
			BottomRow:          ptr("♥"),
		},

		Body: &Body{
			BodyLeft:           "♥",
			BodyRight:          "♥",
			BodySplitter:       "♥",
			BottomLeftCorner:   ptr("♥"),
			BottomRightCorner:  ptr("♥"),
			BottomIntersection: ptr("♥"),
			BottomRow:          ptr("♥"),
		},
	}

	Unicode *Style = &Style{
		Header: &Header{
			TopLeftCorner:      ptr("╔"),
			TopRightCorner:     ptr("╗"),
			TopIntersection:    ptr("╤"),
			TopRow:             ptr("═"),
			HeaderLeft:         "║",
			HeaderSplitter:     "│",
			HeaderRight:        "║",
			BottomLeft:         ptr("╟"),
			BottomRight:        ptr("╢"),
			BottomIntersection: ptr("┼"),
			BottomRow:          ptr("━"),
		},

		Body: &Body{
			BodyLeft:           "║",
			BodyRight:          "║",
			BodySplitter:       "│",
			BottomLeftCorner:   ptr("╚"),
			BottomRightCorner:  ptr("╝"),
			BottomIntersection: ptr("╧"),
			BottomRow:          ptr("═"),
		},
	}

	Idk *Style = &Style{
		Header: &Header{
			TopLeftCorner:      ptr("#"),
			TopRightCorner:     ptr("#"),
			TopIntersection:    ptr("#"),
			TopRow:             ptr("#"),
			HeaderLeft:         "#",
			HeaderSplitter:     "#",
			HeaderRight:        "#",
			BottomLeft:         ptr("#"),
			BottomRight:        ptr("#"),
			BottomIntersection: ptr("#"),
			BottomRow:          ptr("#"),
		},

		Body: &Body{
			BodyLeft:           "#",
			BodyRight:          "#",
			BodySplitter:       "#",
			BottomLeftCorner:   ptr("#"),
			BottomRightCorner:  ptr("#"),
			BottomIntersection: ptr("#"),
			BottomRow:          ptr("#"),
		},
	}

	Compact *Style = &Style{
		Header: &Header{
			TopLeftCorner:      ptr(""),
			TopRightCorner:     ptr(""),
			TopIntersection:    ptr(""),
			TopRow:             ptr(""),
			HeaderLeft:         "",
			HeaderSplitter:     "│",
			HeaderRight:        "│",
			BottomLeft:         ptr("├"),
			BottomRight:        ptr("┤"),
			BottomIntersection: ptr("┴"),
			BottomRow:          ptr("─"),
		},

		Body: &Body{
			BodyLeft:           "",
			BodyRight:          "",
			BodySplitter:       "│",
			BottomLeftCorner:   ptr(""),
			BottomRightCorner:  ptr(""),
			BottomIntersection: ptr(""),
			BottomRow:          ptr(""),
		},
	}

	MySQL *Style = &Style{
		Header: &Header{
			TopLeftCorner:      ptr("+"),
			TopRightCorner:     ptr("+"),
			TopIntersection:    ptr("+"),
			TopRow:             ptr("-"),
			HeaderLeft:         "|",
			HeaderSplitter:     "|",
			HeaderRight:        "|",
			BottomLeft:         ptr("+"),
			BottomRight:        ptr("+"),
			BottomIntersection: ptr("+"),
			BottomRow:          ptr("-"),
		},

		Body: &Body{
			BodyLeft:           "|",
			BodyRight:          "|",
			BodySplitter:       "|",
			BottomLeftCorner:   ptr("+"),
			BottomRightCorner:  ptr("+"),
			BottomIntersection: ptr("+"),
			BottomRow:          ptr("-"),
		},
	}

	Generic *Style = &Style{
		Header: &Header{
			TopLeftCorner:      ptr("┌"),
			TopRightCorner:     ptr("┐"),
			TopIntersection:    ptr("┬"),
			TopRow:             ptr("─"),
			HeaderLeft:         "│",
			HeaderSplitter:     "│",
			HeaderRight:        "│",
			BottomLeft:         ptr("├"),
			BottomRight:        ptr("┤"),
			BottomIntersection: ptr("┼"),
			BottomRow:          ptr("─"),
		},
		Body: &Body{
			BodyLeft:           "│",
			BodyRight:          "│",
			BodySplitter:       "│",
			BottomLeftCorner:   ptr("└"),
			BottomRightCorner:  ptr("┘"),
			BottomIntersection: ptr("┴"),
			BottomRow:          ptr("─"),
		},
	}
)
