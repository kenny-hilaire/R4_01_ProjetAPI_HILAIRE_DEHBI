<?php
    //on met en place une api donc c'est des données sous format json que l'on va utiliser
    header("Content-Type: application/json")
    require_once "connexion.php"
    require_once "jwt_utils.php"


        $data = (array) json_decode(file_get_contents('php://input'),TRUE);
            if (empty($data['login]) || empty($data']))