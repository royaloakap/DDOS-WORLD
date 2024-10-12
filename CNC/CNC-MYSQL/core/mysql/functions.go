package database 


func Check() bool {
	_, Row := Database.Query("SELECT * FROM `users`, `attacks`")

	if Row == nil {
		return true
	} else if Row != nil {
		return false
	} else {
		return true
	}
}