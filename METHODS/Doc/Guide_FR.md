# Les méthodes d'attaque DDoS

Cette section regroupe les implémentations d'attaques – le code que les bots et les C2 utilisent pour exécuter des DDoS. Organisé par couche OSI : L3, L4, L7 et AMP (amplification). Chaque sous-dossier contient des implémentations en C, Python, JavaScript ou autres selon le type d'attaque et la cible.

## Structure

**L3** – Couche 3, réseau. ICMP flood, attaques volumétriques. Bas niveau, paquets bruts. Principalement du C. Sature la bande passante au niveau IP.

**L4** – Couche 4, transport. SYN flood, UDP flood, variantes TCP, junk, NTP, memcached. Mix C et Python. Cible les tables de connexion et les ressources transport. La couche d'attaque la plus courante en pratique.

**L7** – Couche 7, application. HTTP GET/POST floods, Slowloris, CFB, spoof, storm. Beaucoup de JavaScript (Node.js) et du Python. Cible les serveurs web et les apps. Souvent plus efficace par octet que L4 – plus difficile à filtrer.

**AMP** – Attaques par amplification. DNS, NTP, SSDP, CHARGEN, Memcached. Utilise la réflexion : petite requête, grosse réponse. Facteurs d'amplification de 10x à 50 000x. Fichiers .pkt et implémentations C. Sature la bande passante avec peu de capacité bot.

## Quand utiliser quoi

Saturation bande passante : L3 ou AMP.  
Épuisement des connexions : L4 (SYN, UDP).  
Coupure serveur web : L7.  
Impact max, peu de bots : AMP.

METHODS est ce que les bots BOTNET exécutent et que C2/CNC dispatche. OSI-MODEL explique le mapping par couche. ANTI-DDOS défend contre tout ça. SANDBOX est l'endroit pour les tester en sécurité. Usage éducatif uniquement – jamais sur des systèmes que tu ne possèdes pas.
