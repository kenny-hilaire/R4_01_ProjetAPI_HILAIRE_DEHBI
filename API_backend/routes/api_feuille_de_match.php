<?php
use R301\Controleur\ParticipationControleur;
use R301\Modele\Participation\Poste;
use R301\Modele\Participation\TitulaireOuRemplacant;

$ctrl = ParticipationControleur::getInstance();
$sousRoute = $segments[2] ?? null;

// GET /participations/{rencontreId}/feuille
if ($method === 'GET' && $id !== null && $sousRoute === 'feuille') {
    $feuille = $ctrl->getFeuilleDeMatch($id);
    if ($feuille === null) {
        deliver_response(404, 'Feuille de match non trouvée', null);
    } else {
        deliver_response(200, 'Succès', $feuille);
    }

// GET /participations
} elseif ($method === 'GET' && $id === null) {
    $participations = $ctrl->listerToutesLesParticipations();
    deliver_response(200, 'Succès', $participations);

// POST /participations  →  ajouter un joueur à la feuille de match
// Le frontend envoie : joueurId, rencontreId, poste, titulaireOuRemplacant
} elseif ($method === 'POST' && $id === null) {
    if ($role !== 'directeur') {
        deliver_response(403, 'Accès interdit', null);
        exit;
    }
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['joueurId'], $data['rencontreId'], $data['poste'], $data['titulaireOuRemplacant'])) {
        deliver_response(400, 'Données manquantes (joueurId, rencontreId, poste, titulaireOuRemplacant)', null);
        exit;
    }

    $poste = Poste::fromName($data['poste']);
    $tor   = TitulaireOuRemplacant::fromName($data['titulaireOuRemplacant']);

    if ($poste === null || $tor === null) {
        deliver_response(400, 'Valeur invalide pour poste ou titulaireOuRemplacant', null);
        exit;
    }

    $result = $ctrl->assignerUnParticipant(
        (int) $data['joueurId'],
        (int) $data['rencontreId'],
        $poste,
        $tor
    );

    if ($result) {
        deliver_response(201, 'Joueur ajouté à la feuille de match', null);
    } else {
        deliver_response(409, 'Le joueur est déjà sur la feuille ou le poste est déjà occupé', null);
    }

// PUT /participations/{id}  →  modifier poste/statut
// Le frontend envoie : joueurId, poste, titulaireOuRemplacant
} elseif ($method === 'PUT' && $id !== null && $sousRoute === null) {
    if ($role !== 'directeur') {
        deliver_response(403, 'Accès interdit', null);
        exit;
    }
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['poste'], $data['titulaireOuRemplacant'], $data['joueurId'])) {
        deliver_response(400, 'Données manquantes (poste, titulaireOuRemplacant, joueurId)', null);
        exit;
    }

    $poste = Poste::fromName($data['poste']);
    $tor   = TitulaireOuRemplacant::fromName($data['titulaireOuRemplacant']);

    if ($poste === null || $tor === null) {
        deliver_response(400, 'Valeur invalide pour poste ou titulaireOuRemplacant', null);
        exit;
    }

    $result = $ctrl->modifierParticipation($id, $poste, $tor, (int) $data['joueurId']);

    if ($result) {
        deliver_response(200, 'Participation modifiée avec succès', null);
    } else {
        deliver_response(500, 'Erreur lors de la modification', null);
    }

// DELETE /participations/{id}
} elseif ($method === 'DELETE' && $id !== null && $sousRoute === null) {
    if ($role !== 'directeur') {
        deliver_response(403, 'Accès interdit', null);
        exit;
    }
    $result = $ctrl->supprimerLaParticipation($id);
    if ($result) {
        deliver_response(200, 'Participation supprimée', null);
    } else {
        deliver_response(500, 'Erreur lors de la suppression', null);
    }

// PUT /participations/{id}/performance
} elseif ($method === 'PUT' && $id !== null && $sousRoute === 'performance') {
    if ($role !== 'directeur') {
        deliver_response(403, 'Accès interdit', null);
        exit;
    }
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['performance'])) {
        deliver_response(400, 'Donnée manquante (performance)', null);
        exit;
    }
    $result = $ctrl->mettreAJourLaPerformance($id, $data['performance']);
    if ($result) {
        deliver_response(200, 'Performance mise à jour', null);
    } else {
        deliver_response(400, 'Impossible de mettre à jour', null);
    }

// DELETE /participations/{id}/performance
} elseif ($method === 'DELETE' && $id !== null && $sousRoute === 'performance') {
    if ($role !== 'directeur') {
        deliver_response(403, 'Accès interdit', null);
        exit;
    }
    $result = $ctrl->supprimerLaPerformance($id);
    if ($result) {
        deliver_response(200, 'Performance supprimée', null);
    } else {
        deliver_response(400, 'Impossible de supprimer', null);
    }

} else {
    deliver_response(405, 'Méthode ou route non autorisée', null);
}
