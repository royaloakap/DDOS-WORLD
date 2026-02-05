# Les panels CNC – interface utilisateur

Cette section regroupe les panels CNC : les interfaces web avec lesquelles les utilisateurs lancent des attaques, gèrent les plans et consultent les dashboards. Contrairement aux backends C2 bruts, les panels CNC ajoutent une couche user-friendly : login, formulaire d'attaque, choix de méthode, facturation, outils admin.

## Ce qu'il y a dedans

**CNC-MYSQL** – CNC en Go avec backend MySQL. Panel complet : gestion users, plans, API d'attaque, persistance en base. Structure style prod avec plein de modules. Une des plus grosses implémentations du repo.

**EZ** – CNC Go minimal. Setup rapide, moins de features. Bon point de départ si tu veux un panel léger sans la complexité d'une stack complète.

**Gostress-V2** – CNC Go avec UI web (HTML/JS/CSS). Dashboard, login, serveur d'attaque. Simple et autonome. Facile à lancer en local pour des labs.

**NekoCNC** – CNC Go avec son propre layout. Une autre implémentation pour comparer les architectures de panels.

**RoyalCNCV0** – CNC Go avec assets custom (ASCII art, branding royal). Config-driven, intégration SSH. Montre comment le branding et l'UX se superposent à la logique CNC.

**Twilight** – Gros CNC Go avec beaucoup d’assets web. UI moderne (landing, dashboard, admin, API), beaucoup de JS/CSS. Panel complet : login, signup, attaques, tickets, dépôts, etc. L’expérience web la plus aboutie de cette section.

## Flux typique

User se connecte → choisit cible, méthode, durée → lance → CNC envoie la requête au C2/API d'attaque → les bots exécutent. Le CNC gère l'auth, les rate limits, les plans, la facturation. Le C2 gère la comm avec les bots et l'envoi des attaques.

CNC = couche utilisateur. C2 = backend qui parle aux bots. BOT fournit les bots Telegram pour l'automatisation. API (GEOIP, PAPING) alimente les panels pour les lookups et checks.
