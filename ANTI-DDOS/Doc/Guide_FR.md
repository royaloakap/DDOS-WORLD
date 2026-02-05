# Les défenses anti-DDoS

Quand t'es attaqué, faut réagir. Cette section regroupe les outils de défense du projet : scripts bash, proxies, détection en temps réel. Chacun fait son job.

## Ce qu'il y a dedans

**Anti-DDOS** – Script bash qui balance des règles iptables sur ton Linux. Rate limiting, blocage des IPs abusives, un peu de protection SYN flood. Pas de interface, juste des règles qui marchent. Bien pour un serveur nu.

**balooProxy** – Proxy reverse en Go avec protection intégrée. Détecte les bots par fingerprinting, rate limit par IP, envoie un captcha si ça sent le bot. Tu peux brancher Telegram pour les alertes. Utile devant une app web.

**DDoS-Killer** – App Go qui sniffe le trafic, détecte les floods en direct et lance la mitigation. Y'a une interface web pour suivre. Architecture détecteur + mitigateur : il voit l'attaque, il bloque ou throttle la source.

**ddos-protection-script-main** – Scripts bash pour Debian et RHEL. Tune les sysctl, pose des règles iptables, limite les connexions. Rapide à déployer sur un serveur vierge. Complète les autres en durcissant l'OS.

## Dans quel ordre ?

Serveur nu, tu veux le basique : Anti-DDOS ou ddos-protection-script-main.  
Tu protèges une app web : balooProxy devant.  
Tu veux de la détection + mitigation auto : DDoS-Killer.  
Pour du sérieux : combine tout – durcissement OS + proxy + détection.

## En pratique

Ces outils se complètent avec MÉTRIQUES (monitoring) et PLAYBOOKS (procédure d'incident). Les honeypots peuvent alimenter des blacklists si tu collectes des IPs d'attaquants.
