<?php
require_once 'jwt_utils.php';
require_once 'connexionDB.php';
require_once 'functions.php';
    $login;
    $mdp;

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            /*
            file_get_content  permet de récupérer le contenu "brut" du corps de la requête HTTP
            le pb est que cette fonction renvooie un json d'ou le json_decode
            On uttilise un trnastypage (array) pour transformer le json renvoyé en tableau et on passe le true pour valider l'operation
            */
        $data = (array) json_decode(file_get_contents('php://input'),TRUE);
        
        if (!isset($data['login'], $data['password'])) {
            deliver_response(400,'Champs manquants',null);
        exit;
        }
        // si l'utilisateur existe alors on crée le JWT
        if (isValidUser($data['login'], $data['password'])){
            $login = $data['login'];
            $role = $data['role'];
            //: algorithme de hachage + type du jeton
            $headers = array('alg' =>'HS256', 'typ' => 'JWT');
            //๏ $payload : les informations à échanger
            $payload = array('login'=> $login,
            'role' => $role ,
            'exp' =>(time() + 3600));
            // $secret : la clé partagée
            $jwt = generate_jwt($headers, $payload, 'secret');
            deliver_response(200,'Succes',$jwt);

        }else{
            deliver_response(404,'Identifiant non trouvé',null);
        }
    }

    