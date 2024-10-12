package plans

type Plan struct {
	Duration int    `json:"duration"`
	Conns    int    `json:"concurrents"`
	API      bool   `json:"api"`
	Expiry   int    `json:"expiry"`
	Price    int    `json:"price"`
	Fiat     string `json:"fiat"`
}
