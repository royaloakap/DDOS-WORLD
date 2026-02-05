# Les proxies

Cette section regroupe les outils et listes liés aux proxies. Les proxies ont deux rôles dans le contexte DDoS : défense (rate limiting, filtrage, masquage de l'origine) et obfuscation (routage du trafic d'attaque via des proxies pour cacher les IP sources). Tu trouveras des proxies exécutables, des configs et des listes de proxies pour les deux usages.

## Ce qu'il y a dedans

**tcp-proxy** – Proxy Go avec rate limiting et blacklist IP. Bloque les IPs abusives, limite les connexions par IP, bloque certains clients SSH. Intégration Telegram pour logs et alertes. Config via JSON. Usage défensif – se place devant un service pour filtrer le trafic.

**proxy-tcp-beta** – Proxy Go avec package Tun. Même concept : proxying TCP avec comportement configurable. Tests, Makefile, modules Go propres. Une autre implémentation pour comparer.

**RoyalProxy** – Binaire proxy avec config.json et options premium/dlс. Setup custom – voir RoyalProjets.md et RoyalProxySetup.md pour les détails.

**Socks4 / Socks5** – Listes de proxies (proxy.txt, Socks4 Proxies.txt, Socks5 Proxies.txt). Utilisées quand le trafic d'attaque doit passer par des proxies SOCKS. Les botnets utilisent parfois des listes de proxies pour diversifier les IP sources.

**HTTP(S)** – Https Proxies.txt, liste de proxies HTTP/HTTPS. Même idée que SOCKS : routage via proxies HTTP pour obfuscation ou répartition de charge.

**AGENTS** – agents.txt, chaînes User-Agent. Utilisées pour spoof ou faire tourner les User-Agents dans les attaques ou requêtes HTTP.

## Quand utiliser quoi

Défense devant un service : tcp-proxy ou proxy-tcp-beta.  
Routage du trafic d'attaque : listes Socks4, Socks5 ou HTTP(S).  
Spoof User-Agent : AGENTS.

PROXY complète ANTI-DDOS (balooProxy est un proxy défensif). C2 et BOTNET peuvent utiliser les listes de proxies pour dispatcher les attaques. API spoof.php touche au spoofing de headers/proxy.
