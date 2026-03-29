<?php

use R301\API_client\ApiClient;

// On vérifie que c'est un POST avec le contenu et l'id du joueur
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['joueurId'], $_POST['contenu'])) {

    // On envoie le commentaire au backend via POST /joueurs/{id}/commentaires
    $reponse = ApiClient::post('/joueurs/' . (int)$_POST['joueurId'] . '/commentaires', [
        'contenu' => $_POST['contenu'],
    ], $_SESSION['token']);

    if ($reponse['status'] !== 200 && $reponse['status'] !== 201) {
        error_log("Erreur lors de l'ajout du commentaire : " . json_encode($reponse));
    }
}

// On retourne vers la page des commentaires du joueur
if (isset($_POST['joueurId'])) {
    header('Location: ' . BASE_PATH . '/joueur/commentaire?id=' . (int)$_POST['joueurId']);
} else {
    header('Location: ' . BASE_PATH . '/joueur');
}
exit;
