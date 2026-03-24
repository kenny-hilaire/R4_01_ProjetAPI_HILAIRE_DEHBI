<?php

use R301\API_client\ApiClient;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $reponse = ApiClient::delete('/joueurs/' . (int)$_POST['id'], $_SESSION['token']);
    if ($reponse['status'] !== 200 && $reponse['status'] !== 204) {
        error_log("Erreur lors de la suppression du joueur : " . json_encode($reponse));
    }
}

header('Location: ' . BASE_PATH . '/joueur');
exit;
