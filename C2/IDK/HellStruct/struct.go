package HellStruct

var Configuration AutoGenerated

type AutoGenerated struct {
	Bot struct {
		Prefix string `json:"Prefix"`
		Token  string `json:"Token"`
	} `json:"Bot"`
	Database struct {
		Host  string `json:"Host"`
		User  string `json:"User"`
		Pass  string `json:"Pass"`
		Table string `json:"Table"`
	} `json:"Database"`
	Miscellanous struct {
		MaxRunningAttacks int `json:"MaxRunningAttacks"`
	} `json:"Miscellanous"`
	Methods []struct {
		Name        string  `json:"Name"`
		Type        int     `json:"type"`
		Flags       []uint8 `json:"flags"`
		Description string  `json:"Description"`
	} `json:"Methods"`
}
