<?php

use R301\API_client\ApiClient;

// On vérifie que c'est bien un POST avec un id valide
// (on ne supprime jamais sur un GET pour éviter les suppressions accidentelles)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {

    // On appelle le backend pour supprimer le joueur via DELETE /joueurs/{id}
    $reponse = ApiClient::delete('/joueurs/' . (int)$_POST['id'], $_SESSION['token']);

    // Si ni 200 ni 204 (no content) → erreur qu'on log
    if ($reponse['status'] !== 200 && $reponse['status'] !== 204) {
        error_log("Erreur lors de la suppression du joueur : " . json_encode($reponse));
    }
}

// Dans tous les cas on retourne à la liste des joueurs
header('Location: ' . BASE_PATH . '/joueur');
exit;
