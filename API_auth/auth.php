<?php
//on met en place une api donc c'est des données sous format json que l'on va utiliser
header("Content-Type: application/json");
require_once "connexionBD.php";
require_once "jwt_utils.php";
require_once "config.php";

use R301\API_auth\connexionBD;

$linkpdo = new connexionBD();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $data = (array) json_decode(file_get_contents('php://input'),TRUE);
        if (empty($data['login']) || empty($data['password'])){
            deliver_response(400,'Champs manquants', null);
            exit;
        }
    //on verifie ici que l'utilisateur existe dans la base de donnée authentification
    $user = isValidUser($data['login'], $data['password'], $linkpdo->getConnexion());
    if ($user){
        $login = $user['login'];
        $role = $user['role'];
        //: algortihme de hachage du type ainsi que du jeton 
        $headers = array('alg' => 'HS256', 'typ' => 'JWT');
        //payload (contient les informations qui seront à échangés
        $payload = array('login'=> $login,
        'role' => $role, 'exp' =>(time()+3600));
        //$secret : la clé partagée
        $jwt = generate_jwt($headers, $payload,JWT_SECRET);
        deliver_response(200,'Succes', $jwt);
    }else{
        deliver_response(404,'Identifiant non trouvé', null);
    }
}