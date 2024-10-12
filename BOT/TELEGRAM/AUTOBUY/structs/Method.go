package structs

type Method struct {
	Name        string   `json:"Name"`
	Description string   `json:"Description"`
	Plans       []string `json:"Plans"`
	APIs        []string `json:"APIs"`
}
