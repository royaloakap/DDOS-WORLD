package ranks

func GetRole(name string, has bool) *Rank {
	r, ok := Internal[name]
	if !ok {
		return nil
	}
	r.Has = has
	return r
}


