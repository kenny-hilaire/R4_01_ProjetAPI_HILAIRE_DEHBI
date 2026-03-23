<?php
namespace R301\API_auth;

    class connexionBD{

    private $pdo;
    private static ?connexionBD $instance = null;
    
     public function __construct(){
        try {
            $this->pdo = new \PDO("mysql:host=localhost;dbname=authentification_r401;charset=utf8",'root','$iutinfo');
        } catch (Exception $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }      
    }

    public function getConnection() : PDO{
        return $this->pdo;
    }

     public static function getInstance(): connexionBD
    {
        if (self::$instance == null) {
            self::$instance = new connexionBD();
        }
        return self::$instance;
    }
    }