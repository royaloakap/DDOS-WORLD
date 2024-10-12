package functions

import "fmt"

type Toastr struct {
	Icon  string
	Title string
	Text  string
}

func Toast(t Toastr) string {
	return fmt.Sprintf("<script>$(window).on('load', function () {toastr['%s']('%s', '%s', {closeButton: true,tapToDismiss: false,isRtl: false});})</script>", t.Icon, t.Text, t.Title)
}
