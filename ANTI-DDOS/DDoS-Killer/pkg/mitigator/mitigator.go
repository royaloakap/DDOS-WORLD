package mitigator

import (
	"log"
	"github.com/coreos/go-iptables/iptables"
)

func BlockIP(ip string) error {
	ipt, err := iptables.New()
	if err != nil {
		return err
	}

	// Add rule to drop packets from malicious IP
	err = ipt.AppendUnique("filter", "INPUT", "-s", ip, "-j", "DROP")
	if err != nil {
		return err
	}

	log.Printf("Blocked IP: %s", ip)
	return nil
}