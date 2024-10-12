package cnc

var (
	Clist *ClientList = NewClientList()
	Db    *Database   = NewDatabase("127.0.0.1:3306", "kia", "kia", "kia")
)

var (
	MaxRunningAttacks = 5 //HellStruct.Configuration.Miscellanous.MaxRunningAttacks
)
