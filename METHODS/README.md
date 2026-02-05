# METHODS - DDoS Attack Implementations

## Overview

This section holds the actual attack implementations – the code that bots and C2 systems use to execute DDoS attacks. Organized by OSI layer: L3, L4, L7, and AMP (amplification). Each subfolder contains implementations in C, Python, JavaScript, or other languages, depending on the attack type and target.

## Structure

### L3
Layer 3 – network layer. ICMP floods, volumetric attacks. Low-level, raw packets. Mostly C implementations. Saturates bandwidth at the IP layer.

### L4
Layer 4 – transport layer. SYN flood, UDP flood, TCP variants, junk, NTP, memcached. Mix of C and Python. Targets connection tables and transport resources. Most common attack layer in practice.

### L7
Layer 7 – application layer. HTTP GET/POST floods, Slowloris, CFB, spoof, storm. Lots of JavaScript (Node.js) and some Python. Targets web servers and apps. Often more effective per byte than L4 – harder to filter.

### AMP
Amplification attacks. DNS, NTP, SSDP, CHARGEN, Memcached. Uses reflection: small request, huge response. Amplification factors from 10x to 50,000x. Packet files (.pkt) and C implementations. Saturates bandwidth with minimal bot capacity.

## When to Use What

- **Bandwidth saturation**: L3 or AMP
- **Connection exhaustion**: L4 (SYN, UDP)
- **Web server takedown**: L7
- **Max impact, minimal bots**: AMP

## Integration with DDOS-WORLD

**METHODS** is what **BOTNET** bots execute and **C2/CNC** dispatches. **OSI-MODEL** explains the layer mapping. **ANTI-DDOS** defends against these. **SANDBOX** is where you test them safely. Educational use only – never against systems you don't own.

## Documentation

See [Doc/Guide_FR.md](Doc/Guide_FR.md) for a practical guide in French.
