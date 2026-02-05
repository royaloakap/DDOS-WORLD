# SANDBOX - Safe DDoS Lab Environments

## Overview

This section provides sandbox and lab environments for safely experimenting with DDoS attack and defense mechanisms. Isolated, reproducible setups allow learning without impacting real infrastructure or violating legal boundaries.

## Purpose

- **Learning**: Practice attack and defense techniques in a controlled environment
- **Testing**: Validate anti-DDoS configurations before production
- **Research**: Reproduce attack scenarios for analysis
- **Training**: Teach cybersecurity concepts hands-on

## Recommended Repositories

### SODA Lab (Simulation Of DDoS Attacks)
**Repository**: [github.com/KEN-Ver1/soda-lab](https://github.com/KEN-Ver1/soda-lab)  
**Language**: Docker/Shell

DDoS attack simulation lab using Docker containers. Components: Grafana, InfluxDB for monitoring; DVWA for vulnerability testing; baseline generation. Configurable attack scenarios. Designed to test DDoS defense posture.

### DDoS-SandBox (cset-sandbox-poc)
**Repository**: [github.com/DDoS-SandBox/cset-sandbox-poc](https://github.com/DDoS-SandBox/cset-sandbox-poc)  
**Language**: Python/Containernet

Proof-of-concept DDoS sandbox using Containernet. Emulates BGP network with ~30 autonomous systems (ASes). Topology generator for network simulation. Advanced setup for BGP/DDoS research.

### vagrant-docker-sandbox
**Repository**: [github.com/sosedoff/docker-sandbox](https://github.com/sosedoff/docker-sandbox) or search for "vagrant-docker-sandbox"  
**Language**: Vagrant/Docker

General Docker sandbox via Vagrant. Quick `vagrant up` setup. Shared `/playground` directory. Good base to build custom DDoS lab.

### vagrant-labs
**Repository**: [github.com/alex4lbin/vagrant-labs](https://github.com/alex4lbin/vagrant-labs)  
**Language**: Vagrant

Various lab environments: Docker, networking (Cumulus, Linux router, iptables), Ansible. Modular. Can be adapted for DDoS testing.

## Clone Repositories

Run the script to download all repos:

```bash
cd SANDBOX
chmod +x clone_repos.sh
./clone_repos.sh
```

Or clone manually:

```bash
git clone https://github.com/KEN-Ver1/soda-lab.git
git clone https://github.com/DDoS-SandBox/cset-sandbox-poc.git
git clone https://github.com/sosedoff/docker-sandbox.git
git clone https://github.com/alex4lbin/vagrant-labs.git
```

## Typical Lab Architecture

```
[Attacker VM] ---> [Target VM] <--- [Defense/Monitoring VM]
                         |
                    [Metrics: Grafana, Prometheus]
```

## Requirements

- Virtualization: VirtualBox, VMware, or KVM
- RAM: 8–16 GB minimum for full stack
- Docker and/or Vagrant installed
- Network isolation (host-only or isolated VLAN)

## Integration with DDOS-WORLD

Combine with **METHODS** (attack tools), **ANTI-DDOS** (defense), **MÉTRIQUES** (monitoring), **HONEYPOT** (detection). Use sandbox as the safe playground for all components.

## Legal Reminder

Run these labs only on your own hardware or authorized cloud accounts. Never target third-party infrastructure without explicit permission.

## Documentation

See [Doc/Guide_FR.md](Doc/Guide_FR.md) for a practical guide in French.
