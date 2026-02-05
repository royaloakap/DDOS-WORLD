# Les sandbox pour tester le DDoS sans risque

Pour apprendre le DDoS ou tester tes défenses, tu veux pas le faire en prod. Ni sur le réseau de quelqu'un d'autre. Un sandbox, c'est un environnement isolé où tu peux simuler des attaques et des défenses sans toucher au vrai monde.

## Pourquoi un sandbox ?

Parce que tester en prod = mauvaise idée. Et tester sur des cibles externes = illégal. Un lab local ou virtualisé te permet de faire les deux côtés : lancer des attaques (avec les outils de METHODS) et tester des défenses (ANTI-DDOS) dans un réseau fermé. Tu apprends, tu mesures, tu compares, sans risque.

## Les projets qui existent

**SODA Lab** – Simulation of DDoS Attacks. Docker-based, avec Grafana et InfluxDB pour le monitoring, DVWA pour la vulnérabilité. Tu configures des scénarios d'attaque, tu vois l'impact sur les métriques, tu testes tes parades. Bien pour un lab pédagogique.

**DDoS-SandBox (cset-sandbox-poc)** – Plus avancé. Utilise Containernet pour émuler un réseau BGP avec des dizaines de systèmes autonomes. Pour ceux qui veulent simuler des attaques au niveau BGP, blackhole, etc. Setup plus lourd.

**vagrant-docker-sandbox** – Un sandbox Docker basique via Vagrant. `vagrant up` et t'as un environnement prêt. Tu peux le customiser pour ajouter des VMs attaquant/cible. Bon point de départ si tu veux construire ton propre lab.

**vagrant-labs** – Plusieurs environnements Vagrant : Docker, réseau (iptables, routeur Linux), Ansible. Modulaire. Tu peux piocher ce qui t'intéresse pour monter ton lab DDoS.

## Architecture typique

Une VM attaquant, une VM cible, une VM monitoring (Grafana, Prometheus). Tout en réseau host-only ou VLAN isolé. Tu lances une attaque depuis l'attaquant, tu observes les métriques sur le monitoring, tu testes des règles iptables ou nginx sur la cible.

## En pratique

Débutant : SODA Lab, c'est prêt à l'emploi.  
Custom : vagrant-docker-sandbox + tes propres configs.  
Recherche BGP : cset-sandbox-poc.

Rappel : 100% sur ton infra. Jamais sur un hébergeur ou un réseau sans autorisation. C'est du lab, pas du pentest sauvage.

## Télécharger les repos

Depuis le dossier SANDBOX :

```bash
chmod +x clone_repos.sh
./clone_repos.sh
```

Ou en manuel :

```bash
git clone https://github.com/KEN-Ver1/soda-lab.git
git clone https://github.com/DDoS-SandBox/cset-sandbox-poc.git
git clone https://github.com/sosedoff/docker-sandbox.git
git clone https://github.com/alex4lbin/vagrant-labs.git
```
