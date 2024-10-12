// Inside functions package
package functions

import (
    "log"
    "net/http"
    "path/filepath"
    "text/template"
)

func Render(v interface{}, w http.ResponseWriter, file ...string) {
    // Parse the main template
    t, err := template.ParseFiles("assets/html/" + filepath.Join(file...))
    if err != nil {
        log.Println(err)
        return
    }

    t, err = t.ParseFiles("assets/html/nav.html")
    if err != nil {
        log.Println(err)
        return
    }
    t, err = t.ParseFiles("core/footer.html")
    if err != nil {
        log.Println(err)
        return
    }
    t, err = t.ParseFiles("assets/html/construction.html")
    if err != nil {
        log.Println(err)
        return
    }

    // Execute the main template
    err = t.Execute(w, v)
    if err != nil {
        log.Println(err)
        return
    }
}