package plans

type Addon struct {
	Price   int 	`json:"price"`
	Fiat    string  `json:"fiat"`
	Upgrade string  `json:"upgrade"`
	Value   int     `json:"value"`
	Expiry  int     `json:"expiry"`
}
