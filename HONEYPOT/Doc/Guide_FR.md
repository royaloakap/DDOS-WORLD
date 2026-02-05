# Les honeypots pour comprendre le monde des DDoS

Tu veux savoir comment les botnets trouvent leurs cibles ? Ou capturer du trafic Mirai en direct ? Les honeypots sont faits pour ça. En gros, tu simules un serveur vulnérable, tu attends que les bots te scannent, et tu enregistres tout.

## Pourquoi c'est utile ?

La plupart des attaques DDoS partent de botnets IoT : des caméras, des routeurs, des trucs mal sécurisés. Ces bots scannent en permanence le net pour trouver des SSH/Telnet avec des mots de passe par défaut. Quand tu mets un honeypot à la place, tu vois exactement ce qu'ils font : quels ports ils testent, quelles commandes ils lancent, d'où ils viennent.

C'est de la renseignement offensif, mais pour la défense. Tu peux blacklister des IP, comprendre les signatures d'attaque, et te préparer avant d'être ciblé.

## Les projets qui marchent vraiment

**Cowrie** – Le must pour SSH/Telnet. Facile à installer, bien documé, 6k stars sur GitHub. Tu vas capturer 90% des scans Mirai/Gafgyt avec ça. En plus tu peux voir les commandes exécutées, les fichiers uploadés… très instructif.

**T-Pot** – Si tu veux tout en une fois : Cowrie + Dionaea + Conpot + une vingtaine d'autres, avec une interface Elastic/Kibana et une carte des attaques en temps réel. Par contre faut du matos : 8-16 Go de RAM, 128 Go de disque. C'est l'option "lab complet".

**DDoSPot** – Spécialisé amplification UDP : DNS, NTP, SSDP. Parfait pour étudier comment les attaques par réflexion fonctionnent. En Go, assez léger.

**RouterTrap** – Honeypot pour routeurs/IoT, en Rust, avec eBPF. Plus récent, très performant. Pour ceux qui veulent du sérieux.

## En résumé

- Débutant : Cowrie seul, sur une VPS ou un Raspberry Pi  
- Lab complet : T-Pot  
- Étude amplification : DDoSPot  
- Perf + IoT : RouterTrap  

Tout ça c'est du 100% légal tant que c'est sur ton propre réseau. Ne jamais déployer sur l'infra d'un tiers sans autorisation.

## Télécharger les repos

Depuis le dossier HONEYPOT :

```bash
chmod +x clone_repos.sh
./clone_repos.sh
```

Ou en manuel :

```bash
git clone https://github.com/cowrie/cowrie.git
git clone https://github.com/telekom-security/tpotce.git
git clone https://github.com/StopDDoS/ddospot.git
git clone https://github.com/0xinf0/routertrap.git
git clone https://github.com/Botnet-Honeypot/Honeypot.git Botnet-Honeypot
```
