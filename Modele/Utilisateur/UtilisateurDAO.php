<?php

namespace R301\Modele\Utilisateur;

use R301\Modele\DatabaseHandler;
use R301\API_auth\connexionBD;

class UtilisateurDAO {
    private static ?UtilisateurDAO $instance = null;
    private readonly DatabaseHandler $database;
    private connexionBD $authDatabase;

    public function __construct() {
        $this->$authDatabase = connexionBD::getInstance();
        $this->$database = DatabaseHandler::getInstance();
    }

    public static function getInstance(): UtilisateurDAO {
        if (self::$instance == null) {
            self::$instance = new UtilisateurDAO();
        }
        return self::$instance;
    }
}