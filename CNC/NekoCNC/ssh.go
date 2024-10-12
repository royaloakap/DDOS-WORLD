package main

import (
	//"log"
	"fmt"
	"golang.org/x/crypto/ssh"
)

var sshPass string = "7SVPzk!xz4%!"
var sshUser string = "root"
var sshHost string = "66.225.198.218:22"

func nekossh(sshCmd string) (string) {
		//log.Fatalf("Error in ssh.GO")

	myclient, sshsock, err := sshConnect()
	if err != nil {
		fmt.Println(err)
	}
	out, err := sshsock.CombinedOutput(sshCmd)
	if err != nil {
    	fmt.Println(err)
	}
	myclient.Close()
	if err != nil {
		fmt.Println(err)
	}
	lol := fmt.Sprintf(string(out))
	return lol
}

func sshConnect() (*ssh.Client, *ssh.Session, error) {
	

	sshConfig := &ssh.ClientConfig{
		User: sshUser,
		Auth: []ssh.AuthMethod{ssh.Password(sshPass)},
	}
	sshConfig.HostKeyCallback = ssh.InsecureIgnoreHostKey()

	myclient, err := ssh.Dial("tcp", sshHost, sshConfig)
	if err != nil {
		return nil, nil, err
	}

	sshsock, err := myclient.NewSession()
	if err != nil {
		myclient.Close()
		return nil, nil, err
	}

	return myclient, sshsock, nil
}