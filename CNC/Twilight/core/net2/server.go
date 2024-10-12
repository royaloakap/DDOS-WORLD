package server2

import (
    "api/core/database"
    "api/core/models"
    "crypto/rand"
    "crypto/rsa"
    "errors"
    "log"
    "os"

    "golang.org/x/crypto/ssh"
)

var (
    config *ssh.ServerConfig
    logger = log.New(os.Stderr, "[cnc] ", log.Ltime|log.Lshortfile)
)

func Init() {
    config = &ssh.ServerConfig{
        ServerVersion: "SSH-2.0-" + models.Config.Name + "@" + models.Config.Vers,
        PasswordCallback: func(c ssh.ConnMetadata, pass []byte) (*ssh.Permissions, error) {
            user, err := database.Container.GetUser(c.User())
            if err != nil {
                return nil, err
            }
            if !user.IsKey(pass) {
                return nil, errors.New("invalid password")
            }
            return nil, nil
        },
    }

    keyBytes, err := rsa.GenerateKey(rand.Reader, 2048)
    if err != nil {
        logger.Fatal(err)
    }
    key, err := ssh.NewSignerFromSigner(keyBytes)
    if err != nil {
        logger.Fatal(err)
    }
    config.AddHostKey(key)
}
