# OSI-MODEL - DDoS Attacks by Network Layer

## Overview

This section maps DDoS attack vectors to the OSI (Open Systems Interconnection) model. Understanding which layer an attack targets helps choose the right defense strategy and correlate with the **METHODS** section (L3, L4, L7, AMP).

## OSI Layers & DDoS Correlation

| Layer | Name           | DDoS Examples                 | Mitigation Focus         |
|-------|----------------|-------------------------------|--------------------------|
| 7     | Application    | HTTP GET/POST floods, Slowloris | WAF, rate limiting, CDN |
| 6     | Presentation   | Rare (often grouped with L7)  | -                        |
| 5     | Session        | SYN flood (TCP handshake)     | SYN cookies, firewall    |
| 4     | Transport      | UDP flood, amplification      | Rate limiting, scrubbing |
| 3     | Network        | ICMP flood, volumetric        | BGP blackhole, filtering |
| 2     | Data Link      | Rare (layer-specific)         | -                        |
| 1     | Physical       | Rare (cable cutting, etc.)    | Physical security        |

## Recommended Repositories

### DDoS-Attack-Guide
**Repository**: [github.com/Karthikdude/DDoS-Attack-Guide](https://github.com/Karthikdude/DDoS-Attack-Guide)

Comprehensive guide on DDoS types across OSI layers. Covers SYN, UDP, HTTP floods with mitigation strategies: infrastructure protection, application monitoring, rate limiting.

### DoS-Bible
**Repository**: [github.com/Dmitriy-area51/DoS-Bible](https://github.com/Dmitriy-area51/DoS-Bible)

Reference table mapping each OSI layer to attack types, impacts, and mitigation options. Technical reference for researchers.

### OWASP Denial of Service Cheat Sheet
**URL**: [cheatsheetseries.owasp.org/cheatsheets/Denial_of_Service_Cheat_Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Denial_of_Service_Cheat_Sheet.html)

OWASP's official DoS/DDoS cheat sheet. Covers L3, L4, L7 attack surfaces and defenses. Good for application-layer focus.

### Cloudflare Learning - Application Layer DDoS
**URL**: [cloudflare.com/learning/ddos/application-layer-ddos-attack](https://www.cloudflare.com/learning/ddos/application-layer-ddos-attack)

Explains L7 DDoS in depth: why it's effective with less bandwidth, how to detect and mitigate.

## Clone Repositories

Run the script to download all repos:

```bash
cd OSI-MODEL
chmod +x clone_repos.sh
./clone_repos.sh
```

Or clone manually:

```bash
git clone https://github.com/Karthikdude/DDoS-Attack-Guide.git
git clone https://github.com/Dmitriy-area51/DoS-Bible.git
```

*Note: OWASP Cheat Sheet and Cloudflare Learning are web resources, not git repos.*

## DDOS-WORLD Mapping

- **METHODS/L3** → Layer 3 (ICMP, volumetric)
- **METHODS/L4** → Layer 4 (TCP, UDP, SYN)
- **METHODS/L7** → Layer 7 (HTTP, application)
- **METHODS/AMP** → Layer 3–4 (amplification via DNS, NTP, etc.)

## Documentation

See [Doc/Guide_FR.md](Doc/Guide_FR.md) for a practical guide in French.
