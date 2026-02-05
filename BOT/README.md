# BOT - Telegram Automation & Stresser Integration

## Overview

This section holds bot implementations that automate interaction with stresser/CNC systems – typically via Telegram. Users can launch attacks, check status, buy plans, and manage their account from a chat interface instead of a web panel. Common in stresser services where Telegram is the primary delivery channel.

## Components

### Stresser-Telegram
Python bot for Telegram. Uses config.json, methods.json, plan.json for attack configuration. Handles blacklist, redeem codes, running state. Connects to a stresser backend and lets users trigger attacks from the chat. Simple structure – easy to adapt for different backends.

### TELEGRAM/AUTOBUY
Go-based Telegram bot with Sellix integration. Connects to Sellix for payment and plan management. Has database layer, attack structs, config and plans. Full flow: user pays via Sellix → gets plan → can launch attacks via bot. Production-style setup with proper structs and separation.

## When to Use What

- **Simple Telegram stresser bot**: Stresser-Telegram
- **Full flow with payments (Sellix)**: TELEGRAM/AUTOBUY
- **Custom backend**: Use Stresser-Telegram as template and swap the API calls

## Integration with DDOS-WORLD

**BOT** sits between users and the attack infrastructure. Talks to **CNC** or **C2** APIs to launch attacks. **API** (GEOIP, PAPING) may be used for lookups. **SITEWEB** may host the stresser site; bots complement or replace the web UI for some users.

## Documentation

See [Doc/Guide_FR.md](Doc/Guide_FR.md) for a practical guide in French.
