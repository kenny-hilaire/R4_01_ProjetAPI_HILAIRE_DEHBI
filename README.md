## R4.01 — Gestion d'Équipe de Sport
Projet BUT Informatique R4.01 — API REST de gestion d'une équipe de sport avec authentification JWT.

## Lien vers le site 
https://hilaire.alwaysdata.net/

Identifiants:
Directeur = id ==> admin    mdp ==> admin
Joueur = id ==> player mdp ==>admin


## Architecture
3 services hébergés sur 3 comptes AlwaysData distincts :

- API_auth → Authentification JWT (https://api-auth.alwaysdata.net)
- API_backend → API REST gestion équipe (https://r30-api.alwaysdata.net)
- API_FRONT_END → Interface utilisateur (https://hilaire.alwaysdata.net)

## Technologies utilisées
- HTML
- CSS
- PHP
- PDO (pour la gestion de la base de données)
- MySQL


# R4_01_ProjetAPI_HILAIRE_DEHBI
Suite d'un précedent projet PHP qui gérait une équipe de basket. Dans ce projet nous allons mettre ne place les api pour gerer l'authentification et les modification de match et joueur 

## Fonctionnalités principales
- Gestion des joueurs (ajout, modification, suppression, affichage)
- Gestion des matchs (ajout, modification, résultat)
- Ajout de commentaires sur les joueurs et suivi de leur statut (Actif, Blessé, Suspendu, Absent)
- Constitution des feuilles de matchs avec titulaires et remplaçants
- Évaluation des performances des joueurs après chaque match
- Statistiques globales et individuelles pour aider l'entraîneur

## Notes
- Les dates doivent être saisies au format jj/mm/aaaa
- L'accès à l'application nécessite une authentification

## Installation locale (XAMPP)

- Cloner dans htdocs/ProjetAPI/
- Importer API_auth/schema.sql et schema.sql dans phpMyAdmin
- Accéder à http://localhost/ProjetAPI/API_FRONT_END


Auteurs
HILAIRE · DEHBI — BUT Informatique
