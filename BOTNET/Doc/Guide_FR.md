# Les botnets – côté éducatif

Cette section regroupe du code éducatif sur les botnets : comment les bots sont construits, comment ils scannent les cibles, comment les exploits recrutent des appareils. Comprendre ça c'est la base pour se défendre – tu peux pas protéger contre ce que tu connais pas.

## Ce qu'il y a dedans

**BotNet-Goland-1** – Bot en Go. Montre la logique du bot, la communication C2, l'exécution de commandes. Pour étudier comment un bot se connecte au contrôleur et exécute les ordres.

**Cbot** – Exemple client/serveur minimal en C. Le strict minimum pour comprendre : le client reçoit des commandes, le serveur les envoie. Bon point de départ côté protocole.

**moobot** – Codebase botnet complète en C. Bot, serveur CNC, outils de retrieve, scripts de build. Couvre scan, exploitation, envoi de commandes. Un des exemples les plus complets du repo.

**Self Reps** – Modules d'auto-réplication et d'exploits. Scanners et exploits pour différents appareils : ADB, Asus, DLink, GPON, Huawei, Linksys, Realtek, ZTE, Zyxel, etc. Chaque dossier cible une vulnérabilité ou un type d'appareil. Montre comment les botnets trouvent et compromettent l'IoT.

**VULNLIST** – Wordlists et listes utilisées pendant le scan : credentials par défaut, appareils vulnérables connus, listes telnet. Alimente les scanners pour trouver des cibles exploitables.

## Pourquoi c'est là

C'est du matériel pédagogique. La même connaissance sert à construire des défenses, détecter les infections et durcir l'IoT. Ne jamais déployer sur des systèmes que tu ne possèdes pas ou sans autorisation explicite.

BOTNET produit les bots, C2 et CNC les contrôlent. SCAN et HONEYPOT aident à comprendre la reconnaissance et la détection. METHODS définit les attaques que les bots exécutent.
