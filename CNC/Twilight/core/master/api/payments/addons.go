package paymentsapi

import (
    "api/core/database"
    "api/core/master/sessions"
    "api/core/models/server"
    "api/core/models/ranks"
    "api/core/models/plans"
    "encoding/json"
    "net/http"
    "strings"
    "fmt"
)

func init() {
    Route.NewSub(server.NewRoute("/buyaddon", func(w http.ResponseWriter, r *http.Request) {
        type Status struct {
            Status  string `json:"status"`
            Message string `json:"message"`
        }

        switch strings.ToLower(r.Method) {
        case "post":
            ok, user := sessions.IsLoggedIn(w, r)
            if !ok {
                http.Redirect(w, r, "/login", http.StatusTemporaryRedirect)
                return
            }

            addonVal := r.PostFormValue("addon_name")
            fmt.Println("Addon Name:", addonVal) // Log addonVal
            
            addonRanks := []*ranks.Rank{
                ranks.GetRole(addonVal, true),
            }
            fmt.Println("Addon Ranks:", addonRanks) // Log addonRanks
            addon := plans.Addons[addonVal]

        if user.Balance >= addon.Price {
            if err := database.Container.UserUpdateRank(user.User, addonRanks, addon); err != nil {
                json.NewEncoder(w).Encode(&Status{Status: "success", Message: "Successfully purchased addon " + addonVal})
                return
            }
        } else {
            json.NewEncoder(w).Encode(&Status{Status: "error", Message: "Insufficient Balance!"})
        }

            return
        }
    }))
}
