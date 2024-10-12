package meta 

type PresetStart struct {
	Preset []Presets `json:"Presets"`
}

type Presets struct {
	Name string `json:"name"`
	Description string `json:"description"`
	Price string `json:"price"`

	MaxTime int `json:"maxTime"`
	Cooldown int `json:"cooldown"`
	Concurrents int `json:"Concurrents"`
	PowerSavingExempt bool `json:"PowerSavingExempt"`
	BypassBlacklist bool `json:"BypassBlacklist"`
	Admin bool `json:"Admin"`
	Reseller bool `json:"Reseller"`
	VIP bool `json:"VIP"`
	Banned bool `json:"Banned"`
	MaxSessions int `json:"MaxSessions"`
	DefaultDays int `json:"DefaultDays"`
}