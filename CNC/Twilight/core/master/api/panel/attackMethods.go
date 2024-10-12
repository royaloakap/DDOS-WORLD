package panelapi

import (
    "api/core/master/sessions"
    "api/core/models/floods"
    "api/core/models/server"
    "encoding/json"
    "net/http"
    "strings"
)

func init() {
    Route.NewSub(server.NewRoute("/methods", func(w http.ResponseWriter, r *http.Request) {
        if strings.ToLower(r.Method) == "post" {
            ok, user := sessions.IsLoggedIn(w, r)
            if !ok {
                http.Redirect(w, r, "/login", http.StatusTemporaryRedirect)
                return
            }
            type method struct {
                Description string `json:"description"`
                ID          int    `json:"id"`
                Method      string `json:"method"`
                PanelMethod string `json:"panel_method"`
                Subnet      int    `json:"subnet"`
                Type        string `json:"type"`
            }
            type status struct {
                Status  string    `json:"status"`
                Methods []*method `json:"methods"`
            }
            var s = &status{
                Status:  "success",
                Methods: make([]*method, 0),
            }
            if user.HasPermission("vip") {
                for name, meth := range floods.Methods {
                    s.Methods = append(s.Methods, &method{
                        Description: meth.Description,
                        Method:      name,
                        PanelMethod: meth.Name,
                        ID:          0,
                        Subnet:      meth.Subnet,
                        Type: func(t int) string {
                            switch t {
                            case 1:
                                return "UDP (AMP)"
                            case 2:
                                return "UDP"
                            case 3:
                                return "TCP"
                            case 4:
                                return "NETWORK"
                            case 5:
                                return "BOTNET"
                            }
                            return "UNKNOWN"
                        }(meth.Mtype),
                    })
                }
            } else {
                s.Methods = append(s.Methods, &method{
                    Description: "HOME",
                    Method:      "HOME",
                    PanelMethod: "HOME",
                    ID:          0,
                    Subnet:      0, // Adjust subnet value accordingly
                    Type:        "Free", // Adjust type value accordingly
                })
            }
            json.NewEncoder(w).Encode(s)
            return
        } else {
            w.Write([]byte("404 page not found"))
            w.WriteHeader(404)
        }
    }))
}
