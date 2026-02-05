# BOTNET - Educational Botnet & Exploit Materials

## Overview

This section contains educational materials on botnet architecture: how bots are built, how they scan for targets, and how exploits are used to recruit devices into a network. Understanding this is essential for defense – you can't protect against what you don't understand.

## Components

### BotNet-Goland-1
Go-based bot implementation. Demonstrates bot logic, C2 communication, and basic command execution. Used for studying how a bot connects to a controller and carries out instructions.

### Cbot
Simple C client/server botnet example. Minimal code to grasp the core idea: client receives commands, server sends them. Good starting point for understanding the protocol side.

### moobot
Full botnet codebase in C. Includes bot binary, CNC server, retrieve tools, and build scripts. Covers scanning, exploitation, and command dispatch. One of the most complete examples in the repo.

### Self Reps
Self-replication and exploit modules. Scanners and exploits for various devices: ADB, Asus, DLink, GPON, Huawei, Linksys, Realtek, ZTE, Zyxel, etc. Each folder targets a specific vulnerability or device type. Shows how botnets find and compromise IoT gear.

### VULNLIST
Wordlists and lists used during scanning: default credentials, known vulnerable devices, telnet lists. Fed into the scanners to find exploitable targets.

## Educational Purpose

These materials exist to teach how botnets work from the inside. The same knowledge is used to build defenses, detect infections, and harden IoT devices. Never deploy against systems you don't own or have explicit permission to test.

## Integration with DDOS-WORLD

**BOTNET** produces bots; **C2** and **CNC** control them. **SCAN** and **HONEYPOT** help understand reconnaissance and detection. **METHODS** defines what attacks the bots execute.

## Documentation

See [Doc/Guide_FR.md](Doc/Guide_FR.md) for a practical guide in French.
