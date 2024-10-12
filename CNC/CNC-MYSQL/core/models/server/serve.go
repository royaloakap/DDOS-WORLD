package server

import (
	"errors"
	"io/ioutil"
	"log"
	"triton-cnc/core/mysql"
	"triton-cnc/core/models/json/build"
	"triton-cnc/core/models/versions"

	"golang.org/x/crypto/ssh"
)

var ServerCon *ssh.ServerConfig

func New() {
	Config := &ssh.ServerConfig{
		
		PasswordCallback: func(c ssh.ConnMetadata, pass []byte) (*ssh.Permissions, error) {

			Raw, error := database.User_Auth(c.User(), string(pass))
			if !Raw && error != nil {
				return nil, errors.New("database fault")
			} else if !Raw {
				return nil, errors.New("invaild details for "+c.User())
			} else if Raw {
				return nil, nil
			}

			return nil, errors.New("error")
		},

		ServerVersion: "SSH-2.0-OpenSSH_8.6p1 "+versions.GOOS_Edition.Version+" "+versions.GOOS_Edition.Edition,
	}

	Config.MaxAuthTries = build.Config.Masters.Masters_config_maxauthtries

	FileForKey, error := ioutil.ReadFile(versions.GOOS_Edition.AssetsCoreDir+"/ssh-key.ppk")
	if error != nil {
		log.Println("[SSH Fault] [Failed to load `"+versions.GOOS_Edition.AssetsCoreDir+"/ssh-key.ppk`correctly]")
		return
	}

	J_Key, error := ssh.ParsePrivateKey(FileForKey)
	if error != nil {
		log.Println("[SSH Fault] [Failed to load `"+versions.GOOS_Edition.AssetsCoreDir+"/ssh-key.ppk`correctly]")
		return
	}

	Config.AddHostKey(J_Key)

	ServerCon = Config

	Serve()
}