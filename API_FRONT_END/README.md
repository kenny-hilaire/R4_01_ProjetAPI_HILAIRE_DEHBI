# API_FRONT_END — Interface utilisateur

Frontend du projet R4.01 - Gestion d'une équipe de sport.
Ce dossier contient l'IHM (interface utilisateur) qui appelle les APIs backend via HTTP.

## Structure

```
API_FRONT_END/
├── config.php              ← URLs des APIs à configurer ICI
├── index.php               ← Routeur principal
├── .htaccess               ← Réécriture d'URL Apache
├── stylesheet.css
├── Psr4AutoloaderClass.php
├── ApiClient/
│   └── ApiClient.php       ← Classe centrale pour tous les appels cURL
└── Vue/
    ├── login.php
    ├── logout.php
    ├── tableauDeBord.php
    ├── joueur.php
    ├── joueur/
    │   ├── ajouter.php
    │   ├── modifier.php
    │   ├── supprimer.php
    │   ├── commentaire.php
    │   └── commentaire/
    │       ├── ajouter.php
    │       └── supprimer.php
    ├── rencontre.php
    ├── rencontre/
    │   ├── ajouter.php
    │   └── modifier.php
    ├── feuilleDeMatch/
    │   ├── feuilleDeMatch.php
    │   ├── modifier.php
    │   └── evaluation.php
    └── Component/           ← Composants HTML réutilisables (inchangés)
```

## Configuration avant déploiement

Ouvrir `config.php` et renseigner les URLs alwaysdata :

```php
define('AUTH_API_URL',    'https://TON_COMPTE_AUTH.alwaysdata.net/API_auth');
define('BACKEND_API_URL', 'https://TON_COMPTE_BACKEND.alwaysdata.net/API_BACK_END');
define('BASE_PATH',       '/API_FRONT_END');
```

## Déploiement sur alwaysdata

1. Créer un compte alwaysdata **dédié au frontend**
2. Déposer ce dossier `API_FRONT_END/` dans le répertoire `www/` du compte
3. S'assurer que le module `mod_rewrite` est actif (il l'est par défaut sur alwaysdata)
4. Vérifier que PHP a l'extension `curl` activée (c'est le cas par défaut)
5. Mettre à jour `config.php` avec les URLs des deux autres comptes

## Flux de fonctionnement

```
Utilisateur → Frontend (Vue PHP)
                  ↓ appel cURL avec JWT
             Backend API (joueurs, rencontres, etc.)
                  ↓ vérification JWT
             API Auth
```

1. L'utilisateur se connecte via `/login`
2. Le frontend envoie `POST AUTH_API_URL/login` → reçoit un JWT
3. Le JWT est stocké en `$_SESSION['token']`
4. Toutes les pages suivantes transmettent ce JWT dans le header `Authorization: Bearer <token>`

## Format JSON attendu du backend

### GET /joueurs
```json
{
  "status": 200,
  "data": [
    {
      "joueurId": 1,
      "nom": "DUPONT",
      "prenom": "Jean",
      "numeroDeLicence": "00001",
      "dateDeNaissance": "1995-05-12",
      "tailleEnCm": 180,
      "poidsEnKg": 75,
      "statut": "ACTIF"
    }
  ]
}
```

### GET /statistiques
```json
{
  "status": 200,
  "data": {
    "equipe": {
      "nbVictoires": 3,
      "nbNuls": 1,
      "nbDefaites": 2,
      "pourcentageDeVictoires": 50,
      "pourcentageDeNuls": 17,
      "pourcentageDeDefaites": 33
    },
    "joueurs": {
      "1": {
        "posteLePlusPerformant": "JUNGLE",
        "nbRencontresConsecutives": 3,
        "nbTitularisations": 5,
        "nbRemplacant": 1,
        "moyenneDesEvaluations": 3.5,
        "pourcentageDeMatchsGagnes": 60
      }
    }
  }
}
```

### GET /rencontres/:id/feuilleDeMatch
```json
{
  "status": 200,
  "data": {
    "estComplete": false,
    "estEvalue": false,
    "participations": [
      {
        "participationId": 1,
        "poste": "JUNGLE",
        "titulaireOuRemplacant": "TITULAIRE",
        "performance": null,
        "rencontreId": 5,
        "joueur": {
          "joueurId": 2,
          "nom": "MARTIN",
          "prenom": "Paul"
        }
      }
    ]
  }
}
```
