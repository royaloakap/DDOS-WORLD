# Les APIs réseau

Cette section regroupe les APIs utilisées dans les workflows DDoS : GeoIP, tests de connectivité TCP, résolution FiveM. Tout ce dont tu as besoin pour builder des stressers, des panels CNC ou des outils de monitoring.

## Ce qu'il y a dedans

**CFX** – Résout les infos des serveurs FiveM. Récupère joueurs, ping, statut depuis la plateforme CFX. Utile quand tu dois passer d'un host FiveM à une IP ou afficher des données live.

**GEOIP** – Service Go pour les lookups IP → géoloc. Utilise ip2asn pour mapper IP, ASN, pays, réseau. Pratique pour les dashboards qui affichent d'où vient le trafic ou les attaques.

**PAPING&PING** – Test de connectivité TCP. Contrairement au ping ICMP, ça vérifie si un port TCP répond. Pour checker si une cible est up avant/après une attaque, ou pour du diag réseau basique. Implémentation en Go.

**PHP** – Scripts PHP pour les backends API : api.php (générique), mirai.php (handler style Mirai), spoof.php (spoofing de headers). Utilisés dans les stacks stresser/CNC en PHP.

## Quand les utiliser

Dashboard avec carte des sources d'attaque : GEOIP.  
FiveM : CFX pour résoudre les serveurs et afficher les joueurs.  
Avant/après attaque : PAPING pour tester la cible.  
Backend de panel : les scripts PHP.

Ces APIs alimentent les sections CNC, BOT et SITEWEB. Le dossier PAPING contient l'outil standalone ; ici c'est l'API pour l'utiliser de façon programmatique.
