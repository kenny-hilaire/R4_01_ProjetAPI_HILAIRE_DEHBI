<?php

namespace R301\Modele\Utilisateur;

use R301\Modele\DatabaseHandler;

class UtilisateurDAO {
    private static ?UtilisateurDAO $instance = null;
    private readonly DatabaseHandler $database;

    public function __construct() {
        $this->database = DatabaseHandler::getInstance();
    }

    public static function getInstance(): UtilisateurDAO {
        if (self::$instance == null) {
            self::$instance = new UtilisateurDAO();
        }
        return self::$instance;
    }

    public getUtilisateur(string $login){
        $query = 'SELECT * FROM utilisateurs WHERE login = :login';
        $statement=$this->database->pdo()->prepare($query);
        $statement->bindValue(':statut', $statut->name);
        if ($statement->execute()){
            return array_map(
                function($joueur) { return $this->mapToJoueur($joueur); },
                $statement->fetchAll(PDO::FETCH_ASSOC)
            );
        } else {
            exit();
        }
    }
}