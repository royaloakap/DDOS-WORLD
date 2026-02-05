# Les playbooks pour réagir à un DDoS

Quand t'es en plein DDoS, t'as pas le temps de réfléchir. Faut agir vite. Un playbook, c'est une procédure qu'on a écrite à l'avance pour savoir quoi faire dans l'ordre.

## Pourquoi c'est important ?

Sans playbook : panique, tout le monde fait n'importe quoi, tu perds du temps à décider qui fait quoi. Avec playbook : tu suis les étapes, tout le monde sait son rôle, tu réduis le temps de réaction.

Les phases classiques (NIST) : préparation, détection, analyse, confinement, éradication, récupération, post-incident.

## Ce qu'il faut préparer avant

**Avant l'attaque** : identifier les systèmes critiques, définir qui fait quoi (qui appelle l'ISP, qui active le scrubbing, qui communique avec les users), avoir les contacts sous la main (FAI, fournisseur anti-DDoS). Mettre en place des alertes sur les métriques (PPS, BPS) et un seuil d'escalade.

**Pendant** : détecter (monitoring), analyser (type d'attaque, couche OSI, sources), confiner (rate limit, blackhole BGP, activer le scrubbing chez le fournisseur), récupérer (restaurer le trafic légitime, vérifier la stabilité).

**Après** : documenter ce qui s'est passé, mettre à jour les défenses, revoir le playbook si besoin.

## Les ressources qui existent

**AWS Incident Response Playbooks** – AWS publie des playbooks pour ses clients. Y'a un `IRP-DoS.md` dédié aux attaques DoS/DDoS. Même si t'es pas sur AWS, la structure est réutilisable.

**Security Incident Response Playbooks** – Des repos communautaires avec des playbooks pour différents scénarios dont le DDoS. Cherche "incident-response-playbook DDoS" sur GitHub.

**IncidentResponse.com** – Un site avec des playbooks structurés, parfois en PDF/Visio. Bon pour s'inspirer.

**AWS DDoS Best Practices** – Le whitepaper AWS sur la résilience DDoS. Couvre la stratégie de réponse, la création de runbooks, les étapes de préparation.

## En pratique

Ton playbook doit contenir : les rôles, les contacts (FAI, scrubbing), les commandes à lancer (iptables, BGP, etc.), les seuils d'alerte, la procédure de communication interne et externe. Teste-le en simulation une fois par an. Un playbook qu'on a jamais testé, c'est un playbook qui va planter quand t'en as besoin.

## Télécharger les repos

Depuis le dossier PLAYBOOKS :

```bash
chmod +x clone_repos.sh
./clone_repos.sh
```

Ou en manuel :

```bash
git clone https://github.com/aws-samples/aws-incident-response-playbooks.git
git clone https://github.com/certsocietegenerale/IRM.git
```

Le playbook DDoS AWS est dans : `aws-incident-response-playbooks/playbooks/IRP-DoS.md`
