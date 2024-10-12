// Package tun contains the tunneling code.
package tun

import (
	"errors"
	"fmt"
	"io"
	"log"
	"net"
	"os"

	"golang.org/x/crypto/ssh"
)

// Tun is a reverse SSH-tunneling utility.  It connects to a remote machine and creates a listen port.
// any client that connects to the port is forwarded to a target port on the originating host. This
// is essentially a fancy alternative to `ssh -R` which can jump via a chain of multiple hosts before
// opening the listen port on the last host.
type Tun struct {
	config Config
	links  []link
}

// Config for Tun
type Config struct {
	// The secret key used for creating the connections.
	KeyFile string
	// The target incoming connections should be proxied to.  Typically a port on the local host.
	Target string
	// The remote listening address
	RemoteListenAddr string
	// The chain of hosts we should jump through.  Must have at least one
	// element. Elements are of the form user@host:port where all elements are required.
	Chain []string
}

type sshDialerFunc func(network, addr string, config *ssh.ClientConfig) (*ssh.Client, error)

// errors
var (
	ErrChainIsEmpty = errors.New("host chain is empty, must have at least one element")
)

// New tun.
func New(c Config) (*Tun, error) {
	if len(c.Chain) == 0 {
		return nil, ErrChainIsEmpty
	}

	// read the private key
	privateKeyData, err := os.ReadFile(c.KeyFile)

	if err != nil {
		return nil, err
	}

	signer, err := ssh.ParsePrivateKey(privateKeyData)
	if err != nil {
		return nil, err
	}

	// parse the chain and create links
	links := []link{}
	for _, elt := range c.Chain {
		link, err := parseLink(elt)
		if err != nil {
			log.Fatal(err)
		}

		link.sshClientConfig = ssh.ClientConfig{
			User: link.username,
			Auth: []ssh.AuthMethod{
				ssh.PublicKeys(signer),
			},
			HostKeyCallback: ssh.InsecureIgnoreHostKey(),
		}

		links = append(links, link)
	}

	return &Tun{
		config: c,
		links:  links,
	}, nil
}

// Run tun
func (t *Tun) Run() error {
	dial := ssh.Dial

	// Set up the chain
	for n, link := range t.links {
		hostport := link.HostPort()
		log.Printf("dialing hop %d: %s", n+1, hostport)

		conn, err := dial("tcp", hostport, &link.sshClientConfig)
		if err != nil {
			return fmt.Errorf("error dialing [%s]: %v", hostport, err)
		}
		defer conn.Close()

		t.links[n].sshClient = conn
		dial = nextDialer(conn)
	}

	// Set up remote listener using last element of chain
	remoteClient := t.links[len(t.links)-1].sshClient
	listener, err := remoteClient.Listen("tcp", t.config.RemoteListenAddr)
	if err != nil {
		return err
	}
	defer listener.Close()
	log.Printf("listening to %s on %s", t.config.RemoteListenAddr, t.links[len(t.links)-1].HostPort())

	for {
		conn, err := listener.Accept()
		if err != nil {
			log.Fatalf("error accepting connection: %v", err)
		}

		err = t.handleConn(conn)
		if err != nil {
			log.Printf("error handling connection from %s: %v", conn.RemoteAddr().String(), err)
			continue
		}
	}
}

func nextDialer(client *ssh.Client) sshDialerFunc {
	return func(network, addr string, config *ssh.ClientConfig) (*ssh.Client, error) {
		conn, err := client.Dial(network, addr)
		if err != nil {
			return nil, err
		}

		ncc, chans, reqs, err := ssh.NewClientConn(conn, addr, config)
		if err != nil {
			return nil, err
		}

		return ssh.NewClient(ncc, chans, reqs), nil
	}
}

func (t *Tun) handleConn(conn net.Conn) error {
	log.Printf("forwarding connection from %s to %s", conn.RemoteAddr().String(), t.config.Target)
	proxyConn, err := net.Dial("tcp", t.config.Target)
	if err != nil {
		return err
	}

	go func() {
		_, err := io.Copy(proxyConn, conn)
		if err != nil {
			log.Printf("conn -> local error: %v", err)
		}
		log.Printf("closed connection to [%s]", conn.RemoteAddr().String())
	}()

	go func() {
		_, err := io.Copy(conn, proxyConn)
		if err != nil {
			log.Printf("local -> conn error: %v", err)
		}
	}()
	return nil
}
