# Le monitoring Dstat

Cette section regroupe les outils de monitoring et visualisation pour les scénarios DDoS. Graphiques temps réel, métriques par couche (L4, L7), visualisation de l'impact des attaques. Pas le `dstat` CLI classique – ici c'est du monitoring web avec backends PHP et Highcharts pour les graphiques. Tu vois le trafic, les connexions et les patterns d'attaque en direct.

## Ce qu'il y a dedans

**L4/port80** – Setup PHP + Highcharts pour le monitoring Layer 4. Affiche le trafic sur le port 80, les compteurs de connexions, layer4.php et layer7.php pour les données. Config, sources de données, JS pour l'export des charts. Locales FR et EN pour l'UI.

**L7/dstat.php** – Monitoring couche 7. dstat.php suit les métriques applicatives. nginx_status peut alimenter les données de statut. Utilisé quand tu veux voir le trafic L7 (HTTP) et le comportement des connexions pendant les attaques.

## Quand utiliser quoi

Trafic L4 (TCP, connexions) : L4/port80.  
Trafic L7 (HTTP, couche app) : L7/dstat.php.  
Vue complète : lance les deux et corrèle.

Dstat visualise ce que MÉTRIQUES mesure en profondeur. Complète ANTI-DDOS en montrant si la mitigation fonctionne. Les labs SANDBOX peuvent utiliser Dstat pour observer l'impact des attaques en temps réel. Plus simple que Prometheus/Grafana – bien pour des labs rapides ou des démos.
