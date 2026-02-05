# MÉTRIQUES - Network Metrics & DDoS Monitoring

## Overview

This section covers tools and methodologies for monitoring network metrics during DDoS attacks. Understanding PPS (packets per second), BPS (bits per second), and connection rates is essential for detecting attacks, assessing impact, and validating mitigation effectiveness.

## Key Metrics

- **PPS (Packets/sec)**: Volume of packets hitting your infrastructure. SYN floods generate high PPS.
- **BPS (Bits/sec)**: Bandwidth consumption. Volumetric attacks saturate BPS.
- **CPS (Connections/sec)**: New connection attempts. Useful for L7/HTTP floods.
- **Active Connections**: Current half-open or established connections.

## Recommended Repositories

### sFlow-RT DDoS Protect
**Repository**: [github.com/sflow-rt/ddos-protect](https://github.com/sflow-rt/ddos-protect)  
**Language**: JavaScript/Java

Real-time DDoS detection and mitigation. Streams sFlow telemetry from routers, detects floods, triggers BGP RTBH/FlowSpec. Integrates with Prometheus for metrics export.

### sFlow-RT Prometheus
**Repository**: [github.com/sflow-rt/prometheus](https://github.com/sflow-rt/prometheus)  
**Language**: JavaScript

Prometheus exporter for sFlow-RT metrics. Exposes PPS, BPS, top-talkers in Prometheus format. Essential for Grafana dashboards.

### OVH IP Mitigation Exporter
**Repository**: [github.com/hroost/ovh_ip-mitigation_exporter](https://github.com/hroost/ovh_ip-mitigation_exporter)  
**Language**: Go

Prometheus exporter for OVH IP addresses under DDoS mitigation mode. Monitors when OVH's anti-DDoS is active on your IPs.

### sFlow-RT Prometheus + Grafana
**Repository**: [github.com/sflow-rt/prometheus-grafana](https://github.com/sflow-rt/prometheus-grafana)

Docker Compose stack: sFlow-RT + Prometheus + Grafana. Pre-built dashboards for DDoS mitigation, top countries, top networks.

### Grafana DDoS Protect Dashboard
**Dashboard**: [grafana.com/grafana/dashboards/12067](https://grafana.com/grafana/dashboards/12067-sflow-rt-ddos-protect)

Tracks DDoS mitigation actions, RTBH rules, FlowSpec. Visualizes attack detection and response.

## Stack Overview

```
Routers (sFlow) → sFlow-RT → Prometheus → Grafana
                     ↓
              BGP RTBH/FlowSpec (mitigation)
```

## Clone Repositories

Run the script to download all repos:

```bash
cd MÉTRIQUES
chmod +x clone_repos.sh
./clone_repos.sh
```

Or clone manually:

```bash
git clone https://github.com/sflow-rt/ddos-protect.git
git clone https://github.com/sflow-rt/prometheus.git sflow-rt-prometheus
git clone https://github.com/hroost/ovh_ip-mitigation_exporter.git
git clone https://github.com/sflow-rt/prometheus-grafana.git
```

## Integration with DDOS-WORLD

The **Dstat** section provides real-time traffic visualization. This section adds production-grade metrics collection and alerting for lab or operational environments.

## Documentation

See [Doc/Guide_FR.md](Doc/Guide_FR.md) for a practical guide in French.
