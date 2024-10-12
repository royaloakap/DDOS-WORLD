package mix_commands

import "triton-cnc/core/mysql"

func CheckPermissions(command *CommandType, User *database.User) bool {

	if command.Admin && User.Administrator {
		return true
	}

	if command.Reseller && User.Reseller || User.Administrator {
		return true
	}

	if command.Vip && User.Vip || User.Administrator {
		return true
	}
	
	if !command.Vip && !command.Admin && !command.Reseller {
		return true
	}
	return false
}