<?php
header("Content-Type:application/json");
require_once __DIR__ . '/../Psr4AutoloaderClass.php';
require_once __DIR__ . '/../API_auth/jwt_utils.php';
require_once __DIR__ . '/../API_auth/config.php';

use R301\Psr4AutoloaderClass;
$loader = new Psr4AutoloaderClass;
$loader->register();
$loader->addNamespace('R301', __DIR__);

//verification du token
$token = get_bearer_token();
if($token === null || !is_jwt_valid($token, JWT_SECRET)){
     deliver_response(401, "Erreur votre token est null", null);
    exit;
}

// On décode le token pour récupérer le rôle
// 3 parties du token = header.payload.signature
// On prend la partie du milieu (payload) et on la décode
$tokenParts = explode('.', $token);
$payload = json_decode(base64_decode($tokenParts[1]), true);
$role = $payload['role']; // "directeur" ou "joueur"

//on lis la route
$method = $_SERVER['REQUEST_METHOD']; //la methode http est recuperer
$route = $_SERVER['REQUEST_URI']; //on recup l'url complet de la requete

// On nettoie le préfixe /ProjetAPI/API_backend
$basePath = '/ProjetAPI/API_backend';
if (str_starts_with($route, $basePath)) {
    $route = substr($route, strlen($basePath));
    //on utilise substr pour couper le n premiers caractere du basePath de notre url
    //on suprime donc les caractere '/ProjetAPI/API_backend' afin de ne conserver que /joueurs/5 et que ce sois plus facile de traiter la requete
}

// On sépare /joueurs/5 en [joueurs, 5]
$segments = explode('/', trim($route, '/'));
$ressource = $segments[0] ?? '';      // joueurs, rencontres, etc.
$id = isset($segments[1]) && $segments[1] !== '' ? (int)$segments[1] : null;

use R301\Controleur\JoueurControleur;
use R301\Controleur\ParticipationControleur;

switch($ressource){

    case 'joueurs':
        require_once __DIR__.'/routes/apiJoueurs.php';
        break;
    case 'rencontres':
        require_once __DIR__.'/routes/apiRencontres.php';
        break;
    case 'participations':
        require_once __DIR__.'/routes/api_feuille_de_match.php';
        break;
    case 'statistiques':
        require_once __DIR__.'/routes/apiStatistiques.php';
        break;
         // Route inconnue
    default:
        deliver_response(404, 'Route non trouvée', null);
        break;
}



?>