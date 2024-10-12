package plans

type Limits struct {
	Conns    int  `json:"concurrents"`
	Duration int  `json:"duration"`
	API      bool `json:"api"`
	VIP      bool `json:"vip"`
	Servers  int  `json:"servers"`
}
