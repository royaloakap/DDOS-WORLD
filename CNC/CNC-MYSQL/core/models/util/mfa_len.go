package util

func ActiveMFA(MFA string) bool {
	if len(MFA) <= 1 {
		return false
	} else {
		return true
	}
}