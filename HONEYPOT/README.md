# HONEYPOT - DDoS & Botnet Detection

## Overview

This section gathers honeypot solutions designed to detect, analyze, and understand DDoS botnet behavior. Honeypots emulate vulnerable services to attract and log malicious traffic, providing valuable intelligence on attack patterns, scanning activities, and botnet infrastructure.

## Purpose

- **Detection**: Identify botnets scanning for vulnerable targets (Mirai, Gafgyt, etc.)
- **Research**: Study attack techniques and command structures
- **Threat Intelligence**: Collect IPs, payloads, and attack signatures
- **Early Warning**: Get alerts before your real infrastructure is targeted

## Recommended Repositories

### Cowrie
**Repository**: [github.com/cowrie/cowrie](https://github.com/cowrie/cowrie)  
**Stars**: ~6k | **Language**: Python

SSH and Telnet honeypot with medium-to-high interaction. Perfect for capturing IoT botnet brute-force attempts (Mirai-style). Features emulated shell, session logging, and file upload/download capture. Widely used in production.

### T-Pot (Telekom)
**Repository**: [github.com/telekom-security/tpotce](https://github.com/telekom-security/tpotce)  
**Stars**: ~9k | **Language**: Docker/Shell

All-in-one honeypot platform with 20+ honeypots (Cowrie, Dionaea, Conpot, etc.). Includes Elastic Stack for visualization and live attack maps. Deploy with a single script. Requires 8-16GB RAM, 128GB disk.

### DDoSPot
**Repository**: [github.com/StopDDoS/ddospot](https://github.com/StopDDoS/ddospot)  
**Language**: Go

Specialized in UDP-based DDoS amplification attacks. Monitors DNS, NTP, SSDP, CHARGEN services. Plugin-based architecture. Ideal for studying amplification vectors.

### RouterTrap
**Repository**: [github.com/0xinf0/routertrap](https://github.com/0xinf0/routertrap)  
**Language**: Rust

Production-ready honeypot for router/IoT botnet detection. Uses eBPF for performance. Inspired by Cowrie's architecture. Designed to detect DDoS botnets scanning and attacking routers.

### Botnet-Honeypot
**Repository**: [github.com/Botnet-Honeypot/Honeypot](https://github.com/Botnet-Honeypot/Honeypot)  
**Language**: Docker/Python

Docker-based high-interaction honeypot focused on botnet detection. Containerized deployment for quick setup.

## Clone Repositories

Run the script to download all repos:

```bash
cd HONEYPOT
chmod +x clone_repos.sh
./clone_repos.sh
```

Or clone manually:

```bash
git clone https://github.com/cowrie/cowrie.git
git clone https://github.com/telekom-security/tpotce.git
git clone https://github.com/StopDDoS/ddospot.git
git clone https://github.com/0xinf0/routertrap.git
git clone https://github.com/Botnet-Honeypot/Honeypot.git Botnet-Honeypot
```

## Integration with DDOS-WORLD

The `SCAN/scan` component includes `honeypot.c` and `honeypots.txt` for honeypot detection during reconnaissance. Honeypots complement anti-DDoS measures by providing threat intelligence before attacks scale.

## Documentation

See [Doc/Guide_FR.md](Doc/Guide_FR.md) for a practical guide in French.
