<?php

namespace R301\Modele\Utilisateur;

use PDO;
use R301\Modele\DatabaseHandler;
use R301\API_auth\connexionBD;

class UtilisateurDAO {
    private static ?UtilisateurDAO $instance = null;
    private connexionBD $authDatabase;
    private DatabaseHandler $database;

    public function __construct() {
        $this->authDatabase = connexionBD::getInstance();
        $this->database = DatabaseHandler::getInstance();
    }

    public static function getInstance(): UtilisateurDAO {
        if (self::$instance == null) {
            self::$instance = new UtilisateurDAO();
        }
        return self::$instance;
    }

    public function getUtilisateur(string $username): ?Utilisateur {
        $pdo = $this->authDatabase->getConnexion();

        $statement = $pdo->prepare("
            SELECT login, password, role
            FROM user_r401
            WHERE login = :login
        ");
        $statement->bindValue(':login', $username);
        $statement->execute();

        $utilisateur = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$utilisateur) {
            return null;
        }

        return new Utilisateur(
            $utilisateur['login'],
            $utilisateur['password'],
            $utilisateur['role'] ?? null
        );
    }
}