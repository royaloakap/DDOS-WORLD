package structs

type Config struct {
	Token     string   `json:"Token"`
	URL       string   `json:"URL"`
	SellixKey string   `json:"SellixKey"`
	Currency  string   `json:"Currency"`
	Admins    []string `json:"Admins"`
	Blacklist []string `json:"Blacklist"`
	Method    []Method `json:"Methods"`
}
