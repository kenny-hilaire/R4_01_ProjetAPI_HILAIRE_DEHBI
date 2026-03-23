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
$id = isset($segments[1]) ? (int)$segments[1] : null;  // 5 ou null

use R301\Controleur\JoueurControleur;
use R301\Controleur\ParticipationControleur;

switch($ressource){

    case 'joueur':
        $ctrl = JoueurControleur::getInstance();
        if ($method === 'GET' && $id == null){
            //alors c'est la liste de tout les joueurs
            $joueurs = $ctrl->listerTousLesJoueurs();
            var_dump($joueurs);
            deliver_response(200,'Succes',$joueurs);
        }elseif ($method == 'GET' && $id !== null) {
        // GET /joueurs/5 → obtenir un joueur précis
            $joueurs = $ctrl->getJoueurById($id);
            if($joueurs == null){
                deliver_response(404,'Joueur non trouvé',null);
            }else{
                deliver_response(200,'Succes',$joueurs);
            }
        } elseif ($method === 'POST') {
            // POST /joueurs → créer un joueur
            //on verifie que l'utilisateur connecté est l'entraineur
            if ($role !== 'directeur'){
                 deliver_response(403,'Acces interdit',null);
                exit;
            }
            //on recupere le contenue du json pour creer le joueur en appellant le controleur adapté
            $data = json_decode(file_get_contents('php://input'), true);
            $result = $ctrl->ajouterJoueur(
                $data['numero_licence'],
                $data['nom'],
                $data['prenom'],
                new DateTime($data['date_naissance']),
                $data['taille'],
                $data['poids'],
                $data['statut']
            );
            deliver_response(201, 'Joueur créé', $result);
            } elseif ($method === 'PUT' && $id !== null) {
            // PUT /joueurs/5 → modifier un joueur
            //on verifie encore que l'utilisateur est directeur 
            if ($role !== 'directeur') {
                deliver_response(403, 'Accès interdit', null);
                exit;
            }
            $data = json_decode(file_get_contents('php://input'), true);
            $result = $ctrl->modifierJoueur(
                $id,
                $data['numero_licence'],
                $data['nom'],
                $data['prenom'],
                new DateTime($data['date_naissance']),
                $data['taille'],
                $data['poids'],
                $data['statut']
            );
            deliver_response(200, 'Joueur modifié avec succès', $result);
        } elseif ($method === 'DELETE' && $id !== null) {
             // DELETE /joueurs/5 → supprimer un joueur
        // Seulement le directeur
        if ($role !== 'directeur') {
            deliver_response(403, 'Accès interdit', null);
            exit;
        }
        $result = $ctrl->supprimerJoueur($id);
        deliver_response(200, 'Joueur supprimé', $result);

        } else {
            // Méthode non supportée sur cette route
            deliver_response(405, 'Méthode non autorisée', null);
        }
    exit;
        // on gère maintenant les matchs 
}

        //} else {
            // Route inconnue
           // deliver_response(404, 'Route non trouvée', null);
      //  }



?>