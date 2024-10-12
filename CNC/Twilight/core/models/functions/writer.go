package functions

import (
	"encoding/json"
	"net/http"
)

func WriteJson(w http.ResponseWriter, data any) {
	out, err := json.MarshalIndent(data, "", "	")
	if err != nil {
		json.NewEncoder(w).Encode(map[string]any{"error": true, "message": "failed to encode data!"})
		return
	}
	w.Write(out)
}
