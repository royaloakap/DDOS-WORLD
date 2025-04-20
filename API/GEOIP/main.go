package main

import (
	"compress/gzip"
	"encoding/csv"
	"fmt"
	"io"
	"log"
	"net"
	"net/http"
	"os"
	"os/exec"
	"strconv"
	"strings"

	"github.com/gin-gonic/gin"
)

type ASNInfo struct {
	IP            string `json:"ip"`
	ASNumber      int    `json:"as_number,omitempty"`
	ASCountryCode string `json:"as_country_code,omitempty"`
	ASDescription string `json:"as_description,omitempty"`
	RangeStart    string `json:"range_start,omitempty"`
	RangeEnd      string `json:"range_end,omitempty"`
	Whois         string `json:"whois,omitempty"`
	Error         string `json:"error,omitempty"`
}

type IPToASN struct {
	Database []ASNInfo
	DBPath   string
	DBURL    string
}

func (i *IPToASN) UpdateDatabase() error {
	resp, err := http.Get(i.DBURL)
	if err != nil {
		return fmt.Errorf("failed to download database: %w", err)
	}
	defer resp.Body.Close()

	outFile, err := os.Create(i.DBPath)
	if err != nil {
		return fmt.Errorf("failed to create database file: %w", err)
	}
	defer outFile.Close()

	_, err = io.Copy(outFile, resp.Body)
	if err != nil {
		return fmt.Errorf("failed to save database: %w", err)
	}

	log.Println("Database updated successfully.")
	return nil
}

func (i *IPToASN) LoadDatabase() error {
	file, err := os.Open(i.DBPath)
	if err != nil {
		return fmt.Errorf("failed to open database file: %w", err)
	}
	defer file.Close()

	gzipReader, err := gzip.NewReader(file)
	if err != nil {
		return fmt.Errorf("failed to create gzip reader: %w", err)
	}
	defer gzipReader.Close()

	reader := csv.NewReader(gzipReader)
	reader.Comma = '\t'
	records, err := reader.ReadAll()
	if err != nil {
		return fmt.Errorf("failed to read database records: %w", err)
	}

	for _, record := range records {
		asnNumber, _ := strconv.Atoi(record[2])
		i.Database = append(i.Database, ASNInfo{
			RangeStart:    record[0],
			RangeEnd:      record[1],
			ASNumber:      asnNumber,
			ASCountryCode: record[3],
			ASDescription: record[4],
		})
	}
	log.Println("Database loaded successfully.")
	return nil
}

func (i *IPToASN) Query(ip string) *ASNInfo {
	targetIP := net.ParseIP(ip)
	if targetIP == nil {
		return nil
	}

	targetUint := ipToUint(targetIP)

	for _, entry := range i.Database {
		startIP := net.ParseIP(entry.RangeStart)
		endIP := net.ParseIP(entry.RangeEnd)
		if startIP == nil || endIP == nil {
			continue
		}
		startUint := ipToUint(startIP)
		endUint := ipToUint(endIP)

		if targetUint >= startUint && targetUint <= endUint {
			entry.IP = ip
			entry.Whois = getWhoisInfo(ip)
			return &entry
		}
	}
	return nil
}

func ipToUint(ip net.IP) uint32 {
	ip = ip.To4()
	return uint32(ip[0])<<24 + uint32(ip[1])<<16 + uint32(ip[2])<<8 + uint32(ip[3])
}

func resolveDomainToIP(domain string) (string, error) {
	domain = strings.TrimPrefix(domain, "http://")
	domain = strings.TrimPrefix(domain, "https://")
	domain = strings.TrimSuffix(domain, "/")

	addrs, err := net.LookupIP(domain)
	if err != nil || len(addrs) == 0 {
		return "", fmt.Errorf("failed to resolve domain: %w", err)
	}
	return addrs[0].String(), nil
}

func getWhoisInfo(ip string) string {
	cmd := exec.Command("whois", ip)
	output, err := cmd.Output()
	if err != nil {
		log.Printf("Failed to retrieve WHOIS info: %v", err)
		return "WHOIS info not available"
	}
	return string(output)
}

func main() {
	dbPath := "ip2asn.tsv.gz"
	dbURL := "https://iptoasn.com/data/ip2asn-combined.tsv.gz"

	asnDB := &IPToASN{DBPath: dbPath, DBURL: dbURL}

	if err := asnDB.UpdateDatabase(); err != nil {
		log.Fatalf("Error updating database: %v", err)
	}
	if err := asnDB.LoadDatabase(); err != nil {
		log.Fatalf("Error loading database: %v", err)
	}

	r := gin.Default()

	r.GET("/api/ip/*ipOrDomain", func(c *gin.Context) {
		ipOrDomain := strings.TrimPrefix(c.Param("ipOrDomain"), "/")
		ip := ipOrDomain

		if net.ParseIP(ipOrDomain) == nil {
			var err error
			ip, err = resolveDomainToIP(ipOrDomain)
			if err != nil {
				c.JSON(http.StatusBadRequest, ASNInfo{
					Error: fmt.Sprintf("Failed to resolve domain: %s", err),
				})
				return
			}
		}

		asnInfo := asnDB.Query(ip)
		if asnInfo == nil {
			c.JSON(http.StatusNotFound, ASNInfo{
				IP:    ip,
				Error: "IP not found in ASN database",
			})
			return
		}

		c.JSON(http.StatusOK, asnInfo)
	})

	if err := r.Run(":80"); err != nil {
		log.Fatalf("Error starting server: %v", err)
	}
}
