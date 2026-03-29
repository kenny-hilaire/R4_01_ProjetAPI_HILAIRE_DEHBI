<?php
header("Content-Type:application/json");
require_once __DIR__ . '/Psr4AutoloaderClass.php';
require_once __DIR__ . '/jwt_utils.php';
require_once __DIR__ . '/config.php';

use R301\Psr4AutoloaderClass;
$loader = new Psr4AutoloaderClass;
$loader->register();
$loader->addNamespace('R301', __DIR__);

// Fix Linux case-sensitive filesystem (contrairement à Windows/XAMPP)
$loader->addNamespace('R301\\Controleur', __DIR__ . '/CONTROLEUR');
$loader->addNamespace('R301\\Modele', __DIR__ . '/MODELE');
$loader->addNamespace('R301\\Modele\\Joueur', __DIR__ . '/MODELE/Joueur');
$loader->addNamespace('R301\\Modele\\Joueur\\Commentaire', __DIR__ . '/MODELE/Joueur/Commentaire');
$loader->addNamespace('R301\\Modele\\Rencontre', __DIR__ . '/MODELE/Rencontre');
$loader->addNamespace('R301\\Modele\\Participation', __DIR__ . '/MODELE/Participation');
$loader->addNamespace('R301\\Modele\\Statistiques', __DIR__ . '/MODELE/Statistiques');
$loader->addNamespace('R301\\Modele\\Utilisateur', __DIR__ . '/MODELE/Utilisateur');

// Vérification du token
$token = get_bearer_token();
if ($token === null || !is_jwt_valid($token, JWT_SECRET)) {
    deliver_response(401, "Erreur : token invalide ou absent", null);
    exit;
}

// On décode le token pour récupérer le rôle
$tokenParts = explode('.', $token);
$payload = json_decode(base64_decode($tokenParts[1]), true);
$role = $payload['role']; // "directeur" ou "joueur"

// Lecture de la méthode HTTP et de l'URL
$method = $_SERVER['REQUEST_METHOD'];
$route  = $_SERVER['REQUEST_URI'];

// Suppression du query string éventuel (?foo=bar)
if (($pos = strpos($route, '?')) !== false) {
    $route = substr($route, 0, $pos);
}

// Nettoyage du préfixe de déploiement (adapté pour AlwaysData)
$basePath = '/API_backend';
if (str_starts_with($route, $basePath)) {
    $route = substr($route, strlen($basePath));
}

// Découpage de l'URL en segments
$segments  = explode('/', trim($route, '/'));
$ressource = $segments[0] ?? '';

// $id = 2e segment s'il est numérique
$id = isset($segments[1]) && $segments[1] !== '' && ctype_digit($segments[1])
    ? (int)$segments[1]
    : null;

use R301\Controleur\JoueurControleur;
use R301\Controleur\ParticipationControleur;

switch ($ressource) {

    case 'joueurs':
        require_once __DIR__ . '/routes/apiJoueurs.php';
        break;

    case 'rencontres':
        require_once __DIR__ . '/routes/apiRencontres.php';
        break;

    case 'participations':
        require_once __DIR__ . '/routes/api_feuille_de_match.php';
        break;

    case 'statistiques':
        require_once __DIR__ . '/routes/apiStatistiques.php';
        break;

    case 'commentaires':
        require_once __DIR__ . '/routes/apiCommentaires.php';
        break;

    default:
        deliver_response(404, 'Route non trouvée', null);
        break;
}