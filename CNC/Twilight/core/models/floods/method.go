package floods

var (
	Methods map[string]*Method = make(map[string]*Method)
)

type Method struct {
	Type        int
	Name        string
	Sname       string
	Description string
	Subnet      int
	Mtype       int
}

func Get(name string) *Method {
	if _, ok := Methods[name]; !ok {
		return nil
	}
	return Methods[name]
}
