# SCAN - Reconnaissance & Vulnerability Discovery

## Overview

This section holds scanning tools used to discover targets: open ports, vulnerable services, and devices that might be recruited into a botnet. Reconnaissance is the first step – you need to know what's out there before you can attack or defend it.

## Components

### port-scanner
Go-based TCP port scanner. Syntax: `./pscanner IP -p PORT` or `-p all` for full scan. Lightweight, fast. Good for quick checks on a single host or range.

### scan
C-based scanner with Telnet combo support. Scans for vulnerable Telnet credentials, checks honeypots (honeypot.c, honeypots.txt), uses queue and connection pooling. Designed for mass scanning – finds devices with default creds that could be exploited. Tutorial.txt explains the workflow.

### scan-ssh
SSH-focused scanner. Targets SSH services for brute force or credential checking. TUT.txt has usage notes.

### zmap
Integration with ZMap – the high-speed Internet scanner. Scanner.sh and zmap.sh wrap ZMap for bulk scanning. ZMap can scan the whole Internet for a port in minutes. Used when you need scale, not just a single host.

## When to Use What

- **Quick port check on one IP**: port-scanner
- **Finding Telnet/IoT targets**: scan
- **SSH credential discovery**: scan-ssh
- **Large-scale Internet scan**: zmap

## Ethical Note

These tools are for authorized testing only. Scanning networks you don't own or have permission to test is illegal. Use in labs, on your own infrastructure, or under explicit written authorization.

## Integration with DDOS-WORLD

**SCAN** finds targets; **BOTNET** exploits them. **HONEYPOT** detects scanners (scan includes honeypot detection to avoid trapping yourself). **METHODS** defines what happens after a target is found. **API/GEOIP** can enrich scan results with location data.

## Documentation

See [Doc/Guide_FR.md](Doc/Guide_FR.md) for a practical guide in French.
