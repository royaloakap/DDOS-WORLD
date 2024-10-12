package versions

import (
	"triton-cnc/core/models/admin"
)

func init() {

	// works with the license server
	RegCSMEdition(true, &Version{
		Name: "standard",

		Active: true,

		Defaultuser:    "root",
		DefaultPassLen: 10,

		Edition: "standard edition",
		Version: admin.Edition,

		Sessions_Command: true,
		Users_Command:    true,
		Extra_Commands:   true,
		Util_Commands:    true,

		CreditsCommand : false,
		Credits: "",
		AssetsCoreDir: "assets",

		Make: map[string]string{
			"ConfigFile":     "assets/config.json",
			"BrandingFolder": "assets/views",
			"LogDir":         "assets/logs",
			"API_Attack":     "assets/api_attack.json",
			"PlanPresets" :   "assets/plan_preset.json",
			"Themes" :        "assets/themes.json",
			"ThemesFolder" :  "assets/themes",
			"ExtraCommands" : "assets/commands/",

			"BrandingPages" : "assets/views/commands",
		},
	})
}
