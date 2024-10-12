package versions

import "sync"

var (
	Versions   = make(map[string]*Version)
	VersionMux sync.Mutex
)

type Version struct {
	Name string

	Active bool

	Defaultuser string
	DefaultPassLen int

	Edition string
	Version string

	Sessions_Command bool
	Users_Command    bool
	Extra_Commands bool
	Util_Commands bool

	CreditsCommand bool
	Credits string

	AssetsCoreDir string

	Make(map[string]string)
}

func RegCSMEdition(register bool,Version *Version) {

	if !register {
		return
	}
	_, Val := Versions[Version.Name]
	if Val {
		return
	}

	VersionMux.Lock()
	Versions[Version.Name] = Version
	VersionMux.Unlock()
}

func Get(name string) *Version {
	Val := Versions[name]
	return Val
}

var GOOS_Edition *Version

func GetVersion() *Version {
	for _, I := range Versions {
		if I.Active {
			GOOS_Edition = I
			return I
		}
	}

	return nil
}