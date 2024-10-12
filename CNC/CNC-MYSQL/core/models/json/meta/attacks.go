package meta 

type AttackMethod struct {
	Methods []MethodSep `json:"methods"`
}

type MethodSep struct {
	API_Name string `json:"api_name"`
	UrlEncode bool `json:"urlEncode"`
	Target string `json:"target"`
	Methods []string `json:"attack_methods"`

	CustomDefine []CSMDefine `json:"custom_define"`
}

type CSMDefine struct {
	Method string `json:"method"`
	Description string `json:"description"`
	DefaultPort int `json:"default_port"`
	VIPMethod bool `json:"vip_method"`
}