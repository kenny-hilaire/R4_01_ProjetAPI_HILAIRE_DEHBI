## Lien vers la PROD
https://r301.kilya.coop/

Identifiants:
Directeur = id ==> admin    mdp ==> admin
Joueur = id ==> player mdp ==>admin
## Configuration Apache
### MODs à installer
php
php-mysql
rewrite

### Configuration du virtual host
```
<VirtualHost *:80>
    ServerName ${serverName}
    DocumentRoot /var/www/${serverName}

    <Directory "/var/www/${serverName}">
        Options Indexes FollowSymLinks
        AllowOverride None
        Require all grantedd
    </Directory>

    RewriteEngine On
    RewriteCond %{REQUEST_URI} !\.(css|jpg)$
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^ index.php [QSA,L]
</VirtualHost>
```

## Technologies utilisées
- HTML
- CSS
- PHP
- PDO (pour la gestion de la base de données)
- MySQL


# R4_01_ProjetAPI_HILAIRE_DEHBI
Suite d'un précedent projet PHP qui gérait une équipe de basket. Dans ce projet nous allons mettre ne place les api pour gerer l'authentification et les modification de match et joueur 
