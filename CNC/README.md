# CNC - Control Panel & Stresser Interface

## Overview

This section holds CNC (Command and Control) panels – the web interfaces users interact with to launch attacks, manage plans, and view dashboards. Unlike raw C2 backends, CNC panels provide a user-friendly layer: login, attack form, method selection, billing, and admin tools.

## Components

### CNC-MYSQL
Go-based CNC with MySQL backend. Full panel: user management, plans, attack API, database persistence. Production-style structure with many modules. One of the largest implementations in the repo.

### EZ
Minimal Go CNC. Quick setup, fewer features. Good starting point if you want a lightweight panel without the complexity of a full stack.

### Gostress-V2
Go CNC with web UI (HTML/JS/CSS). Dashboard, login, attack server. Simple and self-contained. Easy to run locally for labs.

### NekoCNC
Go CNC with its own layout. Another implementation for comparing panel architectures.

### RoyalCNCV0
Go CNC with custom assets (ASCII art, royal branding). Config-driven, includes SSH integration. Shows how branding and UX are layered on top of core CNC logic.

### Twilight
Large Go CNC with extensive web assets. Modern UI (landing, dashboard, admin, API), lots of JS/CSS. Full-featured panel with login, signup, attacks, tickets, deposits, etc. The most complete web experience in this section.

## Typical Flow

User logs in → selects target, method, duration → hits launch → CNC sends request to C2/attack API → bots execute. CNC handles auth, rate limits, plans, and billing. C2 handles bot communication and attack dispatch.

## Integration with DDOS-WORLD

**CNC** is the user-facing layer. **C2** is the backend that talks to bots. **BOT** provides Telegram bots for automation. **API** (GEOIP, PAPING) feeds into panels for lookups and checks. **SITEWEB** may include standalone stresser sites that use CNC backends.

## Documentation

See [Doc/Guide_FR.md](Doc/Guide_FR.md) for a practical guide in French.
