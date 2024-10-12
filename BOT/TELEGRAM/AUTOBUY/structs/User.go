package structs

type User struct {
	ID     string `json:"ID"`
	Plan   Plan   `json:"Plan"`
	Expiry string `json:"Expiry"`
	Banned bool   `json:"Banned"`
}
