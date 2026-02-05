# PROXY - Proxy Configurations & Lists

## Overview

This section holds proxy-related tools and lists. Proxies serve two roles in DDoS contexts: defense (rate limiting, filtering, hiding origin) and obfuscation (routing attack traffic through proxies to hide source IPs). You'll find executable proxies, configs, and proxy lists for both uses.

## Components

### tcp-proxy
Go proxy server with rate limiting and IP blacklisting. Blocks abusive IPs, limits connections per IP, blocks specific SSH clients. Telegram integration for logs and alerts. Configurable via JSON. Used defensively – sits in front of a service to filter traffic.

### proxy-tcp-beta
Go proxy with Tun package. Similar concept: TCP proxying with configurable behavior. Has tests, Makefile, proper Go modules. Another implementation for comparison.

### RoyalProxy
Proxy binary with config.json and premium/dlс options. Custom setup – see RoyalProjets.md and RoyalProxySetup.md for details.

### Socks4 / Socks5
Proxy lists (proxy.txt, Socks4 Proxies.txt, Socks5 Proxies.txt). Used when attack traffic needs to be routed through SOCKS proxies. Botnets sometimes use proxy lists to diversify source IPs.

### HTTP(S)
Https Proxies.txt – HTTP/HTTPS proxy list. Same idea as SOCKS: routing through HTTP proxies for obfuscation or load distribution.

### AGENTS
agents.txt – User-Agent strings. Used for spoofing or rotating User-Agents in HTTP-based attacks or requests.

## When to Use What

- **Defense in front of a service**: tcp-proxy or proxy-tcp-beta
- **Attack traffic routing**: Socks4, Socks5, or HTTP(S) lists
- **User-Agent spoofing**: AGENTS

## Integration with DDOS-WORLD

**PROXY** complements **ANTI-DDOS** (balooProxy is a defensive proxy). **C2** and **BOTNET** may use proxy lists when dispatching attacks. **API** spoof.php relates to header/proxy spoofing.

## Documentation

See [Doc/Guide_FR.md](Doc/Guide_FR.md) for a practical guide in French.
