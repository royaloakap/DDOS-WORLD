# C2 - Command & Control Infrastructure

## Overview

This section contains C2 (Command and Control) implementations – the backend that bots connect to, receive commands from, and report status to. C2 is the brain; the bots in BOTNET are the hands. Different languages, different designs, same core idea: central control over distributed nodes.

## Components

### IDK
Go-based C2. CLI interface, JSON config for attacks, Discord integration. Lightweight and straightforward. Good for labs where you want to understand the C2 protocol without bloat.

### KryptonC2
Python C2 with modular attack methods. Implements L3 (ICMP, POD), L4 (TCP, UDP, SYN, NTP, etc.), L7 (HTTP flood variants), and game-specific methods (Roblox, VSE). Has a bot payload and uses config files for targets. One of the most complete method libraries.

### Mars-C2
Python C2 with TermFX UI. Terminal-based branding, admin panel, logging. Uses JSON configs for methods and targets. Modern feel, good for understanding how C2 UIs work.

### Myra
C-based C2. Includes attack modules (attacks.c), MySQL integration, plans, rate limiting, Xbox-related logic. Lower level than Python/Go – closer to how Mirai-style C2s are built.

### slovakia
Large Go C2 codebase. Production-style architecture with many modules. For studying how a full-featured C2 is structured at scale.

### smokec2
Python C2. Another implementation with its own structure. Useful for comparing approaches across projects.

### URANIUM-C2
Python C2 split into URANIUM_API and URANIUM_C2. API layer + core logic. Shows how to separate concerns when building C2 infrastructure.

## When to Use What

- **Learning the basics**: IDK or KryptonC2
- **Comparing architectures**: Mars-C2 vs Myra vs slovakia
- **Deep dive on methods**: KryptonC2 (L3, L4, L7, games)
- **Understanding Mirai-style**: Myra (C, low-level)

## Integration with DDOS-WORLD

**BOTNET** provides the bots that connect here. **CNC** offers web panels that sit on top of C2 or replace it. **METHODS** defines the attacks the C2 sends to bots. **SITEWEB** may host the frontend for users.

## Documentation

See [Doc/Guide_FR.md](Doc/Guide_FR.md) for a practical guide in French.
