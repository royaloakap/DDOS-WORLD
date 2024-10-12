package core

import (
	"api/core/models"
	"api/core/models/apis"
	"api/core/models/floods"
	"api/core/models/plans"
	"api/core/models/servers"
	"api/modules/goconfig"
	"encoding/json"
	"fmt"
	"log"
	"path/filepath"
)

var (
	Options = &goconfig.Options{
		Config: goconfig.NewConfig(),
	}
	Vers string = models.Config.Vers
)

func Initialize() {
	Options.Config.NewInclusion(".json", func(content []byte, file string, m map[string]any) error {
		switch file {
		case filepath.Join("assets/config", "config.json"):
			models.Config = new(models.Conf)
			return json.Unmarshal(content, &models.Config)
		case filepath.Join("assets/config", "servers.json"):
			servers.Config = new(servers.Configuration)
			return json.Unmarshal(content, &servers.Config)
		case filepath.Join("assets/config", "methods.json"):
			floods.Methods = make(map[string]*floods.Method)
			json.Unmarshal(content, &floods.Methods)
			for name, method := range floods.Methods {
				method.Sname = name
			}
			return nil
		case filepath.Join("assets/config", "plans.json"):
			if err := json.Unmarshal(content, &plans.GeneralConfig); err != nil {
				return err
			}
			plans.Plans = plans.GeneralConfig.Plans
			plans.Addons = plans.GeneralConfig.Addons
			plans.LimitsConfig = plans.LimitsConfig
			fmt.Println(plans.Plans)
			return nil
		case filepath.Join("assets/config", "apis.json"):
			apis.Apis = make(map[string]*apis.Api)
			json.Unmarshal(content, &apis.Apis)
			return nil
		default:
			fmt.Println(file, m, string(content))
			return json.Unmarshal(content, &m)
		}
	})
	Options.Config.NewInclusion(".sql", func(b []byte, s string, m map[string]any) error {
		return nil
	})

	err := Options.Config.Parse("assets")
	if err != nil {
		log.Println(err)
		return
	}

	Options, err = Options.Config.Options()
	if err != nil {
		log.Println(err)
		return
	}
	return
}
