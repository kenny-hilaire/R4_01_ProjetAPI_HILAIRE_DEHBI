<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/Psr4AutoloaderClass.php';

use R301\Psr4AutoloaderClass;

$loader = new Psr4AutoloaderClass;
$loader->register();
$loader->addNamespace('R301', __DIR__);

// Extraire la route relative (sans le préfixe /API_FRONT_END)
$requestUri = $_SERVER["REQUEST_URI"];
$route = $requestUri;
if (str_starts_with($route, BASE_PATH)) {
    $route = substr($route, strlen(BASE_PATH));
    $route = preg_replace('/\.php$/', '', $route);
}
if ($route === '' || $route === false) {
    $route = '/login';
}

if (preg_match('/\.(?:png|jpg|jpeg|gif|ico|css|js)\??.*$/', $requestUri)) {
    return false;
} else {

session_start();
 
// On vérifie maintenant token au lieu de username 
// Toutes les pages sauf /login nécessitent un token en session 
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
                <a href="<?= BASE_PATH ?>/logout" class="dropbtn" style="color:#f66;">Déconnexion</a>
            </div>
        </nav>
    <?php endif; ?>
    <?php
        require_once __DIR__ . '/Vue' . strtok($route, '?') . '.php';
    } ?>
    </body>
</html>
