package meta 

type Themes struct {
	Themes []CSMTheme `json:"themes"`
}

type CSMTheme struct {
	Name string
	Description string
	ClearWhenChange bool

	BlockedCommands []string

	Views_Prompt string
	Views_HomeClear string
	Views_AttackSplash string
}

