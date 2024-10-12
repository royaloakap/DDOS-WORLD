package database

import (
	"Telegram/structs"
)

var Config structs.Config
var Users []structs.User
var Tokens []structs.Token
var Plans _PLANS
var Running []structs.Attack

type _PLANS struct {
	Plan []structs.Plan `json:"Plans"`
}
