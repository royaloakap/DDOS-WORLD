package structs

type Token struct {
	Token  string `json:"Token"`
	Plan   Plan   `json:"Plan"`
	Expiry int    `json:"Expiry"`
}
