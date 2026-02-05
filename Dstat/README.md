# Dstat - Traffic & Attack Monitoring

## Overview

This section holds monitoring and visualization tools for DDoS scenarios. Real-time charts, layer metrics (L4, L7), and attack impact visualization. Not the classic `dstat` CLI – this is web-based monitoring with PHP backends and Highcharts for graphs. You see traffic, connections, and attack patterns as they happen.

## Components

### L4/port80
PHP + Highcharts setup for Layer 4 monitoring. Shows traffic on port 80, connection counts, layer4.php and layer7.php for data. Has config, data sources, and JS for exporting charts. French and English locales for the UI.

### L7/dstat.php
Layer 7 monitoring. dstat.php tracks application-layer metrics. nginx_status may feed status data. Used when you want to see L7 (HTTP) traffic and connection behavior during attacks.

## When to Use What

- **L4 traffic (TCP, connections)**: L4/port80
- **L7 traffic (HTTP, app layer)**: L7/dstat.php
- **Full picture**: Run both and correlate

## Integration with DDOS-WORLD

**Dstat** visualizes what **MÉTRIQUES** measures in depth. Complements **ANTI-DDOS** by showing if mitigation is working. **SANDBOX** labs can use Dstat to observe attack impact in real time. Simpler than Prometheus/Grafana – good for quick labs or demos.

## Documentation

See [Doc/Guide_FR.md](Doc/Guide_FR.md) for a practical guide in French.
