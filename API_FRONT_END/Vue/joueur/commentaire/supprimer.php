<?php

use R301\API_client\ApiClient;

// On vérifie que c'est un POST avec l'id du commentaire à supprimer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['commentaireId'])) {

    // On appelle le backend via DELETE /commentaires/{id}
    $reponse = ApiClient::delete('/commentaires/' . (int)$_POST['commentaireId'], $_SESSION['token']);

    if ($reponse['status'] !== 200 && $reponse['status'] !== 204) {
        error_log("Erreur lors de la suppression du commentaire : " . json_encode($reponse));
    }
}

// On retourne vers la page des commentaires du joueur
// joueurId est passé en hidden dans le formulaire de suppression
if (isset($_POST['joueurId'])) {
    header('Location: ' . BASE_PATH . '/joueur/commentaire?id=' . (int)$_POST['joueurId']);
} else {
    header('Location: ' . BASE_PATH . '/joueur');
}
exit;
