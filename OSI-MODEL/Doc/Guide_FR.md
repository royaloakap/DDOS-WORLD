# Le modèle OSI et les attaques DDoS

Pour bien comprendre les attaques DDoS, faut savoir sur quelle couche du modèle OSI elles s'attaquent. Chaque couche = des défenses différentes.

## Les couches qui nous intéressent

**Couche 3 (Réseau)** – ICMP flood, attaques volumétriques. Tu satures la bande passante avec des paquets. Défense : blackhole BGP, filtrage au niveau des routeurs.

**Couche 4 (Transport)** – SYN flood (TCP), UDP flood, amplification. Tu épuises les ressources de connexion ou tu envoies des tonnes de petits paquets. Défense : SYN cookies, rate limiting, scrubbing.

**Couche 7 (Application)** – HTTP GET/POST flood, Slowloris. Tu vises l'application directement : requêtes légitimes mais en masse pour cramer le serveur. Moins de bande passante que L3/L4, mais plus difficile à filtrer. Défense : WAF, rate limiting, CDN, captcha.

## Pourquoi c'est important ?

Les attaques L7 sont souvent plus efficaces à petite échelle : 1 Mbps bien ciblé peut faire tomber un serveur là où 100 Mbps en L3 ne fera que ralentir. Par contre L3/L4 sont plus faciles à détecter et mitiger au niveau réseau.

Dans DDOS-WORLD, la section METHODS est organisée comme ça : L3, L4, L7, AMP (amplification). Chaque dossier correspond à une couche.

## Ressources utiles

**DDoS-Attack-Guide** – Guide complet, couche par couche, avec des exemples de mitigation. Bon point de départ.

**DoS-Bible** – Un tableau de référence : couche → type d'attaque → impact → mitigation. Plus technique, pour les gens qui veulent une vue d'ensemble rapide.

**OWASP DoS Cheat Sheet** – Le standard OWASP. Orienté application web mais couvre bien L3, L4, L7.

**Cloudflare Learning** – Leur doc sur les attaques L7. Claire, bien illustrée. Explique pourquoi L7 fait mal avec moins de bande passante.

En résumé : avant de choisir une défense, regarde la couche. Tu défends pas un HTTP flood comme un SYN flood.

## Télécharger les repos

Depuis le dossier OSI-MODEL :

```bash
chmod +x clone_repos.sh
./clone_repos.sh
```

Ou en manuel :

```bash
git clone https://github.com/Karthikdude/DDoS-Attack-Guide.git
git clone https://github.com/Dmitriy-area51/DoS-Bible.git
```
