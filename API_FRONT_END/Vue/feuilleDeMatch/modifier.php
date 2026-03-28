<?php

use R301\API_client\ApiClient;

$token = $_SESSION['token'];

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['action'], $_POST['poste'], $_POST['titulaireOuRemplacant'], $_POST['rencontreId'])
) {
    switch($_POST['action']) {
        case "create":
            if (isset($_POST['joueurId']) && $_POST['joueurId'] !== '') {
                $reponse = ApiClient::post('/participations', [  // ✅ bonne route
                    'joueur_id'               => (int)$_POST['joueurId'],       // ✅ snake_case
                    'rencontre_id'            => (int)$_POST['rencontreId'],    // ✅ ajouté
                    'poste'                   => $_POST['poste'],
                    'titulaire_ou_remplacant' => $_POST['titulaireOuRemplacant'], // ✅ snake_case
                ], $token);
                if ($reponse['status'] !== 200 && $reponse['status'] !== 201) {
                    error_log("Erreur assignation participant : " . json_encode($reponse));
                }
            }
            break;
        case "update":
            if (isset($_POST['participationId'], $_POST['joueurId']) && $_POST['joueurId'] !== '') {
                $reponse = ApiClient::put('/participations/' . (int)$_POST['participationId'], [
                    'joueur_id'               => (int)$_POST['joueurId'],       // ✅ snake_case
                    'poste'                   => $_POST['poste'],
                    'titulaire_ou_remplacant' => $_POST['titulaireOuRemplacant'], // ✅ snake_case
                ], $token);
                if ($reponse['status'] !== 200) {
                    error_log("Erreur modification participation : " . json_encode($reponse));
                }
            }
            break;
        case "delete":
            if (isset($_POST['participationId'])) {
                $reponse = ApiClient::delete('/participations/' . (int)$_POST['participationId'], $token);
                if ($reponse['status'] !== 200 && $reponse['status'] !== 204) {
                    error_log("Erreur suppression participation : " . json_encode($reponse));
                }
            }
            break;
    }
    header('Location: ' . BASE_PATH . '/feuilleDeMatch/feuilleDeMatch?id=' . (int)$_POST['rencontreId']);
} else {
    if (isset($_POST['rencontreId'])) {
        header('Location: ' . BASE_PATH . '/feuilleDeMatch/feuilleDeMatch?id=' . (int)$_POST['rencontreId']);
    } else {
        header('Location: ' . BASE_PATH . '/rencontre');
    }
}
exit;