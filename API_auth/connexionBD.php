<?php
namespace R301\API_auth;

    class connexionBD{

    private $pdo;
    private static ?connexionBD $instance = null;
    
     public function __construct(){
        try {
            $this->pdo = new \PDO("mysql:host=mysql-api-auth.alwaysdata.net;dbname=api-auth_authentification_r401;charset=utf8",'api-auth','ap_auth_Project26*');
        } catch (Exception $e) {
            die("Erreur de connexion : " . $e->getMessage());
        }      
    }

    public function getConnexion() : \PDO{
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