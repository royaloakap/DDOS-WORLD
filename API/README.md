# API - Network Management & Lookup Services

## Overview

This section holds API implementations used in network management and DDoS-related workflows. GeoIP lookups, TCP connectivity checks, FiveM server resolution – stuff you need when building stressers, CNC panels, or monitoring tools.

## Components

### CFX
Resolver for FiveM server information. Fetches server details (players, ping, status) from the CFX platform. Used when you need to resolve a FiveM host to an IP or get live server data.

### GEOIP
Go service for IP-to-geolocation lookups. Uses ip2asn data to map IPs to ASN, country, and network. Handy for dashboards that show where traffic or attacks come from.

### PAPING&PING
TCP connectivity testing. Unlike ICMP ping, this checks if a TCP port is reachable. Used to verify if a target is up before or after an attack, or for basic network diagnostics. Go implementation.

### PHP
PHP scripts for API backends: `api.php` (generic API), `mirai.php` (Mirai-style handler), `spoof.php` (header spoofing). Used in stresser/CNC stacks that run on PHP.

## Typical Use Cases

- **Dashboard**: GEOIP to show attack sources on a map
- **FiveM**: CFX to resolve servers and get player counts
- **Pre/post attack check**: PAPING to test if target is reachable
- **Backend logic**: PHP scripts for panels that need API endpoints

## Integration with DDOS-WORLD

Feeds into **CNC**, **BOT**, and **SITEWEB** panels. **PAPING** folder has the standalone tool; this API wraps or extends it for programmatic use.

## Documentation

See [Doc/Guide_FR.md](Doc/Guide_FR.md) for a practical guide in French.
