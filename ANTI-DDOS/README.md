# ANTI-DDOS - Defense & Mitigation Solutions

## Overview

This section bundles defensive tools and scripts designed to protect infrastructure against DDoS attacks. You'll find traffic analysis, rate limiting, iptables rules, and proxy-based mitigation. Each component tackles a different angle of defense.

## Components

### Anti-DDOS
Bash script that applies iptables rules to harden a Linux system. Limits connection rates, blocks abusive IPs, and implements basic SYN flood protection. Works on Debian/RHEL. No fancy UI – just rules that actually help.

### balooProxy
Go-based reverse proxy with built-in DDoS protection. Uses fingerprinting to detect bots, rate limits per IP, and serves captcha when suspicious. Integrates with Telegram for alerts. Good for protecting web apps behind it.

### DDoS-Killer
Go application that sniffs traffic, detects floods in real time, and triggers mitigation. Has a web UI for monitoring. Uses a detector + mitigator architecture – detects the attack, then blocks or throttles the source.

### ddos-protection-script-main
Bash scripts for Debian and RHEL. Applies sysctl tuning, iptables rules, and connection limits. Quick to deploy on a bare server. Complements the other tools by hardening the OS layer.

## When to Use What

- **Bare server, need basics**: Anti-DDOS or ddos-protection-script-main
- **Web app, need a shield**: balooProxy
- **Want detection + auto-mitigation**: DDoS-Killer
- **Full stack**: Combine OS hardening + proxy + detection

## Integration with DDOS-WORLD

Works with **MÉTRIQUES** for monitoring and **PLAYBOOKS** for incident response. **HONEYPOT** feeds threat intel that can inform blacklists.

## Documentation

See [Doc/Guide_FR.md](Doc/Guide_FR.md) for a practical guide in French.
