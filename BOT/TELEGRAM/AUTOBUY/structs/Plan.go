package structs

type Plan struct {
	Name        string  `json:"Name"`
	Description string  `json:"Description"`
	Price       float64 `json:"Price"`
	MaxTime     int     `json:"MaxTime"`
	MaxCons     int     `json:"MaxCons"`
	Rank        int     `json:"Rank"`
}
