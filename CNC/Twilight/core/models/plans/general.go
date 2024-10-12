package plans

type General struct {
	Plans  map[string]*Plan  `json:"plans"`
	Addons map[string]*Addon `json:"addons"`
	*Limits
}
