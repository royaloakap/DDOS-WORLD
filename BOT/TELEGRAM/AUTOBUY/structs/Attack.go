package structs

type Attack struct {
	Host string `json:"Host"`
	Port int    `json:"Port"`
	Time int    `json:"Time"`
	From string `json:"From"`
}
