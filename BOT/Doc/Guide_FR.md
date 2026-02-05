# Les bots Telegram

Cette section regroupe les bots qui automatisent l'interaction avec les systèmes stresser/CNC – souvent via Telegram. Les users lancent des attaques, checkent le statut, achètent des plans et gèrent leur compte depuis un chat au lieu d'un panel web. C'est courant dans les services stresser où Telegram est le canal principal.

## Ce qu'il y a dedans

**Stresser-Telegram** – Bot Python pour Telegram. Utilise config.json, methods.json, plan.json pour la config des attaques. Gère blacklist, redeem codes, état running. Se connecte à un backend stresser et permet aux users de lancer des attaques depuis le chat. Structure simple – facile à adapter pour d'autres backends.

**TELEGRAM/AUTOBUY** – Bot Telegram en Go avec intégration Sellix. Se connecte à Sellix pour paiements et gestion des plans. Couche base de données, structs d'attaque, config et plans. Flux complet : user paie via Sellix → obtient un plan → peut lancer des attaques via le bot. Setup style prod avec structs propres et séparation des responsabilités.

## Quand utiliser quoi

Bot stresser Telegram simple : Stresser-Telegram.  
Flux complet avec paiements (Sellix) : TELEGRAM/AUTOBUY.  
Backend custom : utilise Stresser-Telegram comme base et change les appels API.

BOT se place entre les users et l'infra d'attaque. Parle aux APIs CNC ou C2 pour lancer les attaques. API (GEOIP, PAPING) peut servir pour les lookups. SITEWEB peut héberger le site stresser ; les bots complètent ou remplacent l'UI web pour certains users.
