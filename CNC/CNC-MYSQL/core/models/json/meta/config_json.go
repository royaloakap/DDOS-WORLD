package meta 

type ConfigMeta struct {

	Masters struct {
		MastersHostPort string `json:"masters_host"`
		Masters_config_maxauthtries int `json:"masters_config_maxauthtries"`
	} `json:"masters"`

	Database struct {
		Sql_host string `json:"sql_host"`
		Sql_username string `json:"sql_username"`
		Sql_password string `json:"sql_password"`
		Sql_name string `json:"sql_name"`
	}

	AppConfig struct {
		AppName string `json:"app_name"`
		AppColour string `json:"app_sig_colour"`
	} `json:"app_config"`

	Disabled_commands []string `json:"Disabled_commands"`

	Extra struct {
		TableType string `json:"TableType"`
		TitleSpinner bool `json:"TitleSpinner"`
		TitleSpinnerFrames []string `json:"TitleSpinnerFrames"`
		DefaultColours string `json:"DefaultColours"`
	} `json:"Extra"`

	UserDefaults struct {
		Admin bool `json:"Admin"`
		Reseller bool `json:"Reseller"`
		VIP bool `json:"VIP"`
		Banned bool `json:"Banned"`
		MaxSessions int `json:"MaxSessions"`
		PowerSavingExempt bool `json:"PowerSavingExempt"`
		BypassBlacklist bool `json:"BypassBlacklist"`
		DefaultDaysLeft int `json:"DefaultDays"`
	} `json:"User_Defaults"`
}