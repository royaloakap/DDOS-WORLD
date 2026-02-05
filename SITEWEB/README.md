# SITEWEB - Stresser & Panel Templates

## Overview

This section holds web templates and frontends for stresser services. Landing pages, dashboards, payment integration, attack forms – the stuff users see when they visit a stresser site. Different designs, different stacks (PHP, Laravel, vanilla JS), all serving the same purpose: provide a web interface for DDoS-as-a-service.

## Components

### Stresser1–9
Multiple stresser template variants. Each has its own layout, assets, and structure. Stresser1 is large (2800+ files) with Bootstrap, Morris.js, CKEditor. Stresser3, Stresser8, Stresser9 use PHP backends. Stresser5–7 share similar layouts. Pick one as a base and adapt.

### nstress
Full stresser hub with CKEditor, plugins, many JS/CSS assets. More complete than a simple landing – includes admin, user area, and attack interface.

### stress
Landing page template with Bootstrap, Owl Carousel, Fancybox. Coin icons for payment methods. Single-page style – good for a minimal stresser front.

### EliteC2 / EliteC2P2
C2-themed web interfaces. PNG assets, JS, CSS. Branding for C2 dashboards or panels.

### Cyclone / cypher / Site1 / RoyalAPI
Smaller templates – landing pages, API docs, or minimal sites. Good starting points for custom builds.

### Zopz Website Src
Single HTML file. Ultra-minimal template.

## When to Use What

- **Full stresser site**: nstress or Stresser8/9
- **Landing only**: stress or Site1
- **C2 branding**: EliteC2
- **Minimal**: Cyclone or Zopz

## Integration with DDOS-WORLD

**SITEWEB** is the frontend. **CNC** provides the backend logic. **BOT** can replace or complement the web UI. **API** feeds GEOIP, PAPING, etc. into the panels. These templates are for educational study – adapt for your lab, never for illegal services.

## Documentation

See [Doc/Guide_FR.md](Doc/Guide_FR.md) for a practical guide in French.
