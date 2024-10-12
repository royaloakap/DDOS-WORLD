package functions

import (
	"api/core/database"
	"encoding/json"
	"net/http"
)

func GetQuery(r *http.Request, key string) (bool, string) {
	if !r.URL.Query().Has(key) {
		return false, ""
	}
	return true, r.URL.Query().Get(key)
}

func GetKey(w http.ResponseWriter, r *http.Request) (*database.User, bool) {
	ok, user := GetQuery(r, "user")
	if !ok {
		json.NewEncoder(w).Encode(map[string]any{"error": true, "message": "missing query \"?user=user\""})
		return nil, false
	}
	ok, key := GetQuery(r, "key")
	if !ok {
		json.NewEncoder(w).Encode(map[string]any{"error": true, "message": "missing query \"?key=key\""})
		return nil, false
	}
	skey, err := database.Container.GetUser(user)
	if err != nil {
		json.NewEncoder(w).Encode(map[string]any{"error": true, "message": err})
		return nil, false
	}
	if skey == nil {
		json.NewEncoder(w).Encode(map[string]any{"error": true, "message": "database error occured!"})
		return nil, false
	}
	if !skey.IsKey([]byte(key)) {
		json.NewEncoder(w).Encode(map[string]any{"error": true, "message": "invalid key provided!"})
		return nil, false
	}
	return skey, true
}

func GetQuerys(w http.ResponseWriter, r *http.Request, query map[string]bool) map[string]string {
	values := make(map[string]string)
	for name, required := range query {
		if !r.URL.Query().Has(name) && !required {
			continue
		} else if !r.URL.Query().Has(name) && required {
			json.NewEncoder(w).Encode(map[string]any{"error": true, "message": "missing required query field \"" + name + "\""})
			return nil
		}
		values[name] = r.URL.Query().Get(name)
	}
	return values
}
