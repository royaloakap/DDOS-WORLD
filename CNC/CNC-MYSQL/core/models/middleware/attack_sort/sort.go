package attacksort

import (
	"sync"
	"triton-cnc/core/models/json/build"
)


var (
	Methods_Map = make(map[string]*Method)
	MuxMap      sync.Mutex
)


func Get(method string) *Method {
	return Methods_Map[method]
}

type Method struct {
	MethodName string

	Target_API string
	UrlEncode  bool


	VIP_Method bool
	DefaultPort int
}

func SortMets() {
	for I := 0; I < len(build.AttackAPI.Methods); I++ {


		for _, Methods := range build.AttackAPI.Methods[I].Methods {
		
			var NewAttack = Method {
				MethodName: Methods,
				Target_API: build.AttackAPI.Methods[I].Target,
				UrlEncode: build.AttackAPI.Methods[I].UrlEncode,

				VIP_Method: false,
				DefaultPort: 0,
			}
			for L := 0; L < len(build.AttackAPI.Methods[I].CustomDefine); L++ {
				if build.AttackAPI.Methods[I].CustomDefine[L].Method == Methods {
					NewAttack.DefaultPort = build.AttackAPI.Methods[I].CustomDefine[L].DefaultPort
					NewAttack.VIP_Method = build.AttackAPI.Methods[I].CustomDefine[L].VIPMethod
				}
			}

			MuxMap.Lock()
			Methods_Map[Methods] = &NewAttack
			MuxMap.Unlock()

			continue
		}
	}
}