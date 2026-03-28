<!-- Routeur principal : toutes les URLs passent par ce fichier -->

<?php
// On charge le fichier de config qui contient les URLs des APIs et BASE_PATH
require_once __DIR__ . '/config.php';

// On charge l'autoloader qui permet de trouver automatiquement les classes PHP
// sans avoir à faire des require_once partout
require_once __DIR__ . '/Psr4AutoloaderClass.php';

use R301\Psr4AutoloaderClass;

// On crée l'autoloader et on lui dit que les classes R301 se trouvent dans ce dossier
$loader = new Psr4AutoloaderClass;
$loader->register();
$loader->addNamespace('R301', __DIR__);

// On récupère l'URL complète demandée par le navigateur
// Ex: /ProjetAPI/API_FRONT_END/joueur/modifier?id=5
$requestUri = $_SERVER["REQUEST_URI"];
$route = $requestUri;

// On enlève le préfixe BASE_PATH (/ProjetAPI/API_FRONT_END) de l'URL
// pour garder seulement la partie utile
// Ex: /ProjetAPI/API_FRONT_END/joueur → /joueur
if (str_starts_with($route, BASE_PATH)) {
    $route = substr($route, strlen(BASE_PATH));
    // On enlève aussi l'extension .php si elle est présente dans l'URL
    $route = preg_replace('/\.php$/', '', $route);
}

// Si l'URL est vide (l'utilisateur est allé sur /API_FRONT_END sans rien après)
// on le redirige vers le login
if ($route === '' || $route === false) {
    $route = '/login';
}

// Si c'est un fichier statique (image, CSS, JS), on le sert directement sans passer par le routeur
if (preg_match('/\.(?:png|jpg|jpeg|gif|ico|css|js)\??.*$/', $requestUri)) {
    return false;
} else {

// On démarre la session PHP pour pouvoir utiliser $_SESSION
// C'est là qu'on stocke le token JWT après connexion
session_start();

// Vérification de connexion : si l'utilisateur n'est pas sur /login
// ET qu'il n'a pas de token en session → il n'est pas connecté → on le renvoie au login
if (strtok($route, '?') !== "/login" && !isset($_SESSION['token'])) {
    header('Location: ' . BASE_PATH . '/login');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>R4.01 - Équipe de sport</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0" charset="UTF-8"/>
        <link rel="stylesheet" href="<?= BASE_PATH ?>/stylesheet.css"/>
    </head>
    <body>
    <!-- On affiche la navbar seulement si on n'est pas sur la page de login -->
    <?php if (strtok($route, '?') !== '/login') : ?>
        <nav class="navbar">
            <a href="<?= BASE_PATH ?>/tableauDeBord" class="dropbtn">Tableau de bord</a>
            <div class="dropdown">
                <button class="dropbtn">Joueurs</button>
                <div class="dropdown-content">
                    <a href="<?= BASE_PATH ?>/joueur/ajouter">Ajouter un joueur</a>
                    <a href="<?= BASE_PATH ?>/joueur">Liste de joueurs</a>
                </div>
            </div>
            <div class="dropdown">
                <button class="dropbtn">Rencontres</button>
                <div class="dropdown-content">
                    <a href="<?= BASE_PATH ?>/rencontre/ajouter">Ajouter une rencontre</a>
                    <a href="<?= BASE_PATH ?>/rencontre">Liste des rencontres</a>
                </div>
            </div>
            <div class="dropdown">
                <!-- Lien de déconnexion en rouge -->
                <a href="<?= BASE_PATH ?>/logout" class="dropbtn" style="color:#f66;">Déconnexion</a>
            </div>
        </nav>
    <?php endif; ?>
    <?php
        // C'est ici que la magie opère :
        // On inclut dynamiquement le fichier Vue qui correspond à la route demandée
        // Ex: route = /joueur        → inclut Vue/joueur.php
        // Ex: route = /joueur/ajouter → inclut Vue/joueur/ajouter.php
        // Ex: route = /rencontre     → inclut Vue/rencontre.php
        require_once __DIR__ . '/Vue' . strtok($route, '?') . '.php';
    } ?>
    </body>
</html>
