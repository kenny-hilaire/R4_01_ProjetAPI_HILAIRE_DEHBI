<?php
require_once '../v1/functions.php';

   try {
    $linkpdo = new PDO(
    "mysql:host=127.0.0.1;port=3306;dbname=authentification_r401;charset=utf8",
    "root",
    ""
    );
    } catch(PDOException $e) {
        die("Erreur lors** de la connexion à la bd : " . $e->getMessage());
    }
    //une d=fois avoir fais un prepare on utilise un beginTransaction()/ un lastInsertId(= , commit)

      function isValidUser($login, $password){
        global $linkpdo;

        $stmt = $linkpdo->prepare(
            'SELECT login, password, role FROM user_r401 WHERE login = :login'
        );
        $stmt->execute([
            'login'=> $login
        ]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            return $user; // on renvoie les infos utiles
        }
        return false;
    }

?>
