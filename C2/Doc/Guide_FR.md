# Le C2 – Command & Control

Cette section regroupe des implémentations C2 : le backend auquel les bots se connectent, reçoivent les commandes et envoient le statut. Le C2 c'est le cerveau, les bots dans BOTNET sont les mains. Différents langages, différentes approches, même idée : contrôle central sur des nœuds distribués.

## Ce qu'il y a dedans

**IDK** – C2 en Go. Interface CLI, config JSON pour les attaques, intégration Discord. Léger et direct. Bien pour les labs où tu veux comprendre le protocole C2 sans prise de tête.

**KryptonC2** – C2 Python avec méthodes d'attaque modulaires. L3 (ICMP, POD), L4 (TCP, UDP, SYN, NTP…), L7 (variantes HTTP flood), méthodes pour jeux (Roblox, VSE). Payload bot inclus, config pour les cibles. Une des bibliothèques de méthodes les plus complètes.

**Mars-C2** – C2 Python avec interface TermFX. Branding terminal, panel admin, logging. Configs JSON pour méthodes et cibles. Look moderne, bien pour voir comment fonctionnent les UIs C2.

**Myra** – C2 en C. Modules d'attaque, MySQL, plans, rate limiting, logique Xbox. Plus bas niveau que Python/Go – proche de la façon dont les C2 style Mirai sont construits.

**slovakia** – Gros codebase C2 en Go. Architecture style prod avec plein de modules. Pour étudier comment un C2 complet est structuré à grande échelle.

**smokec2** – C2 Python. Une autre implémentation avec sa propre structure. Utile pour comparer les approches entre projets.

**URANIUM-C2** – C2 Python découpé en URANIUM_API et URANIUM_C2. Couche API + logique cœur. Montre comment séparer les responsabilités dans une infra C2.

## Pour quoi faire

Apprendre les bases : IDK ou KryptonC2.  
Comparer les architectures : Mars-C2 vs Myra vs slovakia.  
Creuser les méthodes : KryptonC2 (L3, L4, L7, jeux).  
Comprendre le style Mirai : Myra (C, bas niveau).

BOTNET fournit les bots qui se connectent ici. CNC propose des panels web au-dessus ou à la place du C2. METHODS définit les attaques que le C2 envoie aux bots.
