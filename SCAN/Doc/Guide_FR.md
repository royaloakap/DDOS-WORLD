# Le scanning – reconnaissance et découverte

Cette section regroupe les outils de scan pour découvrir les cibles : ports ouverts, services vulnérables, appareils qu'on pourrait recruter dans un botnet. La reconnaissance c'est la première étape – faut savoir ce qui existe avant d'attaquer ou de se défendre.

## Ce qu'il y a dedans

**port-scanner** – Scanner TCP en Go. Syntaxe : `./pscanner IP -p PORT` ou `-p all` pour tout scanner. Léger et rapide. Bien pour des checks rapides sur un host ou une plage.

**scan** – Scanner en C avec support combo Telnet. Cherche des credentials Telnet vulnérables, vérifie les honeypots (honeypot.c, honeypots.txt), utilise une queue et un pool de connexions. Conçu pour le scan de masse – trouve les appareils avec creds par défaut exploitables. Tutorial.txt explique le workflow.

**scan-ssh** – Scanner centré SSH. Cible les services SSH pour brute force ou vérification de credentials. TUT.txt a les notes d'utilisation.

**zmap** – Intégration avec ZMap, le scanner Internet haute vitesse. Scanner.sh et zmap.sh wrappent ZMap pour du scan en masse. ZMap peut scanner tout Internet sur un port en quelques minutes. Pour la scale, pas juste un host.

## Quand utiliser quoi

Check rapide sur une IP : port-scanner.  
Trouver des cibles Telnet/IoT : scan.  
Découverte de credentials SSH : scan-ssh.  
Scan Internet à grande échelle : zmap.

## Rappel légal

Ces outils sont pour des tests autorisés uniquement. Scanner des réseaux que tu ne possèdes pas ou sans permission est illégal. Utilise en lab, sur ta propre infra, ou avec une autorisation écrite explicite.

SCAN trouve les cibles, BOTNET les exploite. HONEYPOT détecte les scanners (scan inclut de la détection honeypot pour ne pas te piéger toi-même). METHODS définit ce qui se passe une fois la cible trouvée.
