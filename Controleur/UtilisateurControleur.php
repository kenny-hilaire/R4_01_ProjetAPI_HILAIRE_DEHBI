<?php

namespace R301\Controleur;

use DateTime;
use R301\Modele\Joueur\Commentaire\Commentaire;
use R301\Modele\Joueur\Commentaire\CommentaireDAO;
use R301\Modele\Joueur\Joueur;
use R301\Modele\Joueur\JoueurDAO;
use R301\Modele\Joueur\JoueurStatut;
use R301\Modele\Statistiques\StatistiquesEquipe;
use R301\Modele\Statistiques\StatistiquesJoueurs;

class UtilisateurControleur {
    private static ?UtilisateurControleur $instance = null;

    private function __construct() {
    }

    public static function getInstance(): UtilisateurControleur {
        if (self::$instance == null) {
            self::$instance = new UtilisateurControleur();
        }
        return self::$instance;
    }

    public function seConnecter(string $username, string $password): bool {
    //on prepare les donnee à envoyer    
    $donnees = json_encode([
        'login' => $username,
        'password' => $password
    ]);
     //on va utiliser l'outil curl pour envoyer des requetes
    $curl = curl_init();
    curl_setopt_array($curl, [
        // l'URL de notre API auth
        CURLOPT_URL => 'http://localhost/ProjetAPI/API_auth/auth.php',
        // on dit que c'est un POST
        CURLOPT_POST => true,
        // on met notre JSON dans le body
        CURLOPT_POSTFIELDS => $donnees,
        // on dit à curl de nous retourner la réponse
        // sans ça curl affiche la réponse directement au lieu de la stocker
        CURLOPT_RETURNTRANSFER => true,
        // on précise que c'est du JSON qu'on envoie
        CURLOPT_HTTPHEADER => ['Content-Type: application/json']
    ]);
     //  On envoie la requête
    // $reponse contiendra par exemple {"status_code":200,"status_message":"Succes","data":"eyJ..."}
    $reponse = curl_exec($curl);
    curl_close($curl);

    // On convertit le JSON reçu en tableau PHP
    $reponseDecodee = json_decode($reponse, true);

    // ÉTAPE 4 - On traite la réponse
    if ($reponseDecodee['status_code'] == 200) {
        // Connexion réussie !
        // On stocke le token JWT en session pour les prochaines requêtes
        $_SESSION['token'] = $reponseDecodee['data'];
        // On stocke aussi le username pour l'afficher dans l'interface
        $_SESSION['username'] = $username;
        return true;
    } else {
        // Mauvais login ou password
        return false;
    }
    }
}