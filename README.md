# Système de Gestion de Pharmacie avec Symfony - Projet IFOAD 2024-2025

Ce projet est une application web dynamique développée avec Symfony dans le cadre du module de Programmation Web Dynamique (LSIA_S4, IFOAD, 2024-2025). Il s'agit d'une réécriture d'un système de gestion de pharmacie existant en PHP, adaptée pour évaluer les compétences en développement web. L'application distingue deux types d'utilisateurs : les **admins** (administrateurs) et les **patients** (caissiers ou clients), chacun avec des rôles et accès spécifiques.

## Prérequis
- **PHP** : Version 8.2.0 ou supérieure
- **Composer** : Gestionnaire de dépendances PHP
- **Symfony CLI** : Outil de ligne de commande pour Symfony
- **MySQL** : Ou un autre SGBD compatible 
- **Environnement de développement** : Un serveur web local (ex. : XAMPP, WAMP) ou l'utilisation de `symfony server:start`

## Fonctionnalités
### Rôles et Permissions
- **Patients** (rôle par défaut après inscription) :
  - Consulter les médicaments.
  - Consulter les catégories de médicaments.
  - Ajouter des médicaments à un panier.
  - Valider un achat (finaliser le panier).
  - Voir l'historique des achats effectués.
  - Modifier leur profil personnel.
  - **Note** : Un admin doit approuver manuellement pour changer un patient en admin.
- **Admins** (accès total, nécessitant une promotion manuelle) :
  - Gérer les médicaments : Ajouter, modifier, supprimer, afficher, rechercher.
  - Gérer les catégories de médicaments : Ajouter, modifier, supprimer, afficher, rechercher.
  - Gérer les utilisateurs : Ajouter, modifier, supprimer, afficher, rechercher (y compris changer les rôles).
  - Gérer les ventes : Ajouter, modifier, supprimer, afficher, rechercher.
  - Approuver les médicaments nécessitant une ordonnance.
  - Accéder au tableau de bord avec des statistiques et graphiques (ex. : nombre de médicaments, total des ventes).

## Modalités
- **Réalisation** : Projet individuel, à soumettre sous forme d'une archive ZIP.
- **Nom de l'archive** : `JOSEPH_BAGA_IFOAD_2024-2025`.
- **Contenu de l'archive** : Tous les fichiers du code source + le fichier SQL de la base de données (`gestionpharm.sql`).
- **Soumission** : Envoyer l'archive à l'adresse `email` avec comme objet `Projet_Web_PHP_IFOAD`.
- **Date limite** : **13 juin 2025, 11h59 (GMT)**. Seule la première soumission sera prise en compte.

## Installation
### Étape 1 : Préparation de l'environnement
1. Installez **PHP 8.2.0** ou supérieur, **Composer**, et **Symfony CLI** sur votre machine.
2. Configurez un SGBD (ex. : MySQL) et créez une base de données vide.

### Étape 2 : Clonage du projet
1. Téléchargez ou clonez le dépôt GitHub : `git clone https://github.com/Baga-code/Gestion-de-pharmacie-avec-symfony.git` 
2. Déplacez-vous dans le répertoire du projet : `cd Gestion-de-pharmacie-avec-symfony`.

### Étape 3 : Configuration de la base de données
1. Exécutez le fichier SQL :
   - Ouvrez votre SGBD (ex. : phpMyAdmin ou MySQL Workbench).
   - Importez le fichier `gestionpharm..sql` situé à la racine du projet pour initialiser la base avec le schéma et les données.
2. Modifiez le fichier `.env` à la racine du projet :
   - Mettez à jour les variables `DATABASE_URL` avec vos identifiants (ex. : `mysql://username:password@127.0.0.1:3306/gestionPharm?serverVersion=10.11.2-MariaDB&charset=utf8mb4"`).

### Étape 4 : Installation des dépendances
1. Exécutez la commande suivante dans le terminal : `composer install`

2. Assurez-vous que toutes les dépendances Symfony (y compris Bootstrap) sont installées.

### Étape 5 : Lancement du projet
1. Démarrez le serveur de développement :
- Option recommandée : `symfony server:start`
- Alternative : `php -S localhost:8000 -t public`
2. Accédez à l'application via : `http://localhost:8000`.

## Utilisation
### Inscription et Connexion
- **Inscription** :
- Accédez à `/inscription` pour créer un compte. Par défaut, un nouveau compte a le rôle de **patient**.
- Un **admin** doit se connecter pour promouvoir un utilisateur au rôle **admin** via la gestion des utilisateurs.
- **Connexion** :
- **Administrateur** :
 - URL : `http://localhost:8000/login`
 - Email : `admin01@gmail.com`
 - Mot de passe : `joseph01`
- **Patient (Caissier)** :
 - URL : `http://localhost:8000/login`
 - Email : `final@gmail.com` / `admin@joseph.ci`
 - Mot de passe : `1234` / `1234`

### Navigation
- **Patients** :
1. Consultez les médicaments et catégories via les menus correspondants.
2. Ajoutez des médicaments au panier, validez l'achat, et consultez vos achats passés.
3. Modifiez votre profil via le lien dédié.
- **Admins** :
1. Accédez à tous les modules (Médicaments, Catégories, Utilisateurs, Ventes) pour gérer les données.
2. Approuvez les médicaments avec ordonnance requis dans la section approbations.
3. Consultez le tableau de bord pour les statistiques.

### Responsivité
- Le design est responsive grâce à **Bootstrap 5.3.2**, testé sur PC, tablette et smartphone. Ajustez la taille de votre fenêtre pour vérifier l'adaptation. (n'est pas encore effective priée rester sur la version pc)

## Bonus
- Le projet respecte des design patterns (ex. : MVC via Symfony, événements pour la gestion des données).
- Ajout de commentaires dans les fichiers PHP pour une meilleure lisibilité.

## Contributions
Les contributions sont bienvenues ! Ouvrez une issue ou soumettez une pull request sur GitHub pour toute suggestion ou correction de bugs.

## Contact
- **Nom** : JOSEPH BAGA
- **Email** : josephbaga45@gmail.com
- **GitHub** : [baga-code](https://github.com/baga-code)