<?php

use R301\ApiClient\ApiClient;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['joueurId'], $_POST['contenu'])) {
    $reponse = ApiClient::post('/joueurs/' . (int)$_POST['joueurId'] . '/commentaires', [
        'contenu' => $_POST['contenu'],
    ], $_SESSION['token']);

    if ($reponse['status'] !== 200 && $reponse['status'] !== 201) {
        error_log("Erreur lors de l'ajout du commentaire : " . json_encode($reponse));
    }
}

if (isset($_POST['joueurId'])) {
    header('Location: ' . BASE_PATH . '/joueur/commentaire?id=' . (int)$_POST['joueurId']);
} else {
    header('Location: ' . BASE_PATH . '/joueur');
}
exit;
