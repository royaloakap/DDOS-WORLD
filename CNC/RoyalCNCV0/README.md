# RoyalCNCV0 - Panneau de Contrôle C2

## Description
RoyalCNCV0 est un panneau de contrôle C2 (Command & Control) développé en Go. Il permet de gérer des utilisateurs, de lancer des attaques réseau et d'administrer un système centralisé via une interface SSH.

## Fonctionnalités

### Gestion des utilisateurs
- Création et suppression de comptes utilisateurs
- Gestion des permissions (admin, VIP, utilisateur standard)
- Système d'expiration des comptes (jours, heures, mois, années, à vie)
- Gestion des cooldowns entre les attaques
- Limitation du nombre d'attaques concurrentes par utilisateur

### Système d'attaques
- Support de multiples méthodes d'attaque configurables
- Intégration avec des API externes
- Limitation des slots d'attaque globaux
- Protection anti-spam pour les cibles
- Système de blacklist d'IP

### Interface administrateur
- Panneau de commandes complet
- Statistiques en temps réel
- Gestion des utilisateurs connectés
- Visualisation des attaques en cours

## Installation

### Prérequis
- Go 1.17 ou supérieur
- MySQL/MariaDB
- Accès SSH

### Configuration
1. Clonez le dépôt
2. Configurez le fichier `config.royal` avec vos paramètres
3. Importez le schéma de base de données depuis `database.sql`
4. Générez une clé SSH et placez-la dans le dossier `ssh/`

### Démarrage
go build -o RoyalCNCV0
./RoyalCNCV0


## Configuration

Le fichier `config.royal` contient les paramètres principaux:

name=royal
attacksenabled=true
slots=5
generatehelp=true
generatemethods=true
license=VOTRE_LICENCE
port=2137
mysqlhost=localhost
mysqluser=utilisateur
mysqlpassword=motdepasse
mysqldb=betacnc


## Commandes disponibles

### Commandes utilisateur
- `methods` - Affiche les méthodes d'attaque disponibles
- `help` - Affiche l'aide
- `attack` - Lance une attaque

### Commandes administrateur
- `users` - Gestion des utilisateurs
- `adddays` - Ajoute des jours à un utilisateur
- `block` - Bloque une IP
- `unblock` - Débloque une IP
- `cooldown` - Définit le cooldown d'un utilisateur
- `conc` - Définit le nombre d'attaques concurrentes
- `vip` - Donne le statut VIP à un utilisateur
- `private` - Donne le statut PRIVÉ à un utilisateur

## Sécurité
- Authentification par mot de passe avec hachage Argon2
- Connexion SSH sécurisée
- Système de licence pour vérifier l'authenticité

## Support
Pour toute assistance, veuillez ouvrir un ticket sur notre serveur Discord: discord.gg/RoyalC2

## Licence
Ce logiciel est protégé par une licence propriétaire. Une clé de licence valide est requise pour son fonctionnement.

## Auteur
Développé par Royaloakap