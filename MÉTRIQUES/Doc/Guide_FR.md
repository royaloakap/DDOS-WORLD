# Les métriques réseau pour le DDoS

Quand une attaque DDoS démarre, comment tu sais que ça va mal ? Et comment tu mesures si tes défenses marchent ? Réponse : les métriques.

## Les chiffres qui comptent

**PPS** (packets per second) – Le nombre de paquets qui arrivent chaque seconde. Les SYN flood en génèrent des tonnes parce qu'ils balancent des petits paquets à gogo. Si ton PPS explose sans raison, c'est suspect.

**BPS** (bits per second) – La bande passante consommée. Les attaques volumétriques (amplification DNS, NTP) saturent ça. Tu peux avoir du 100 Gbps sans problème.

**CPS** (connections per second) – Les nouvelles tentatives de connexion. Les HTTP floods sur L7 s'en servent pour épuiser le serveur. Beaucoup de connexions = beaucoup de ressources consommées.

**Connexions actives** – Combien de connexions sont en cours. Un serveur qui garde des milliers de connexions half-open (SYN flood) finit par craquer.

## Les outils qui marchent

**sFlow-RT** – Tu fais remonter le télémétrie de tes routeurs (sFlow), et sFlow-RT détecte les attaques en temps réel. Il peut même pousser des règles BGP (RTBH, FlowSpec) pour bloquer le trafic. C'est du sérieux, utilisé en prod.

**Prometheus + Grafana** – La stack classique. Prometheus récupère les métriques, Grafana les affiche. sFlow-RT a un exporter Prometheus, donc tu peux tout centraliser.

**OVH IP Mitigation Exporter** – Si tu es chez OVH et que leur anti-DDoS s'active sur tes IP, cet exporter te dit quand c'est le cas. Pratique pour savoir si t'as été attaqué sans checker manuellement.

## En pratique

Pour un lab : Docker Compose avec sFlow-RT + Prometheus + Grafana. Tu peux simuler du trafic et voir les métriques bouger.

Pour la prod : sFlow sur les routeurs → sFlow-RT → détection automatique → BGP vers le fournisseur pour blackholer. Et Prometheus/Grafana pour l'historique et les alertes.

Le dashboard Grafana "sFlow-RT DDoS Protect" (ID 12067) est un bon point de départ : il affiche les actions de mitigation, les top sources, etc.

## Télécharger les repos

Depuis le dossier MÉTRIQUES :

```bash
chmod +x clone_repos.sh
./clone_repos.sh
```

Ou en manuel :

```bash
git clone https://github.com/sflow-rt/ddos-protect.git
git clone https://github.com/sflow-rt/prometheus.git sflow-rt-prometheus
git clone https://github.com/hroost/ovh_ip-mitigation_exporter.git
git clone https://github.com/sflow-rt/prometheus-grafana.git
```
