<?php

use R301\ApiClient\ApiClient;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['commentaireId'])) {
    $reponse = ApiClient::delete('/commentaires/' . (int)$_POST['commentaireId'], $_SESSION['token']);
    if ($reponse['status'] !== 200 && $reponse['status'] !== 204) {
        error_log("Erreur lors de la suppression du commentaire : " . json_encode($reponse));
    }
}

if (isset($_POST['joueurId'])) {
    header('Location: ' . BASE_PATH . '/joueur/commentaire?id=' . (int)$_POST['joueurId']);
} else {
    header('Location: ' . BASE_PATH . '/joueur');
}
exit;
