<?php
use R301\Controleur\ParticipationControleur;
use R301\Modele\Participation\Poste;
use R301\Modele\Participation\TitulaireOuRemplacant;

$ctrl = ParticipationControleur::getInstance();

// On a besoin du 3e segment pour les sous-routes comme /participations/5/feuille
$sousRoute = $segments[2] ?? null;

// ─── GET /participations ─────────────────────────────────────────────────────
if ($method === 'GET' && $id === null && $sousRoute === null) {
    $participations = $ctrl->listerToutesLesParticipations();
    deliver_response(200, 'Succès', $participations);

// ─── GET /participations/{rencontreId}/feuille ───────────────────────────────
} elseif ($method === 'GET' && $id !== null && $sousRoute === 'feuille') {
    $feuille = $ctrl->getFeuilleDeMatch($id);
    if ($feuille === null) {
        deliver_response(404, 'Feuille de match non trouvée', null);
    } else {
        deliver_response(200, 'Succès', $feuille);
    }

// ─── POST /participations ────────────────────────────────────────────────────
// Ajoute un joueur à la feuille de match d'une rencontre
} elseif ($method === 'POST' && $id === null) {
    if ($role !== 'directeur') {
        deliver_response(403, 'Accès interdit', null);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['joueur_id'], $data['rencontre_id'], $data['poste'], $data['titulaire_ou_remplacant'])) {
        deliver_response(400, 'Données manquantes (joueur_id, rencontre_id, poste, titulaire_ou_remplacant)', null);
        exit;
    }

    $poste = Poste::fromName($data['poste']);
    $tor   = TitulaireOuRemplacant::fromName($data['titulaire_ou_remplacant']);

    if ($poste === null || $tor === null) {
        deliver_response(400, 'Valeur invalide pour poste ou titulaire_ou_remplacant', null);
        exit;
    }

    $result = $ctrl->assignerUnParticipant(
        (int) $data['joueur_id'],
        (int) $data['rencontre_id'],
        $poste,
        $tor
    );

    if ($result) {
        deliver_response(201, 'Joueur ajouté à la feuille de match', null);
    } else {
        deliver_response(409, 'Le joueur est déjà sur la feuille ou le poste est déjà occupé', null);
    }

// ─── PUT /participations/{participationId} ───────────────────────────────────
// Modifie le poste / statut titulaire d'une participation
} elseif ($method === 'PUT' && $id !== null && $sousRoute === null) {
    if ($role !== 'directeur') {
        deliver_response(403, 'Accès interdit', null);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['poste'], $data['titulaire_ou_remplacant'], $data['joueur_id'])) {
        deliver_response(400, 'Données manquantes (poste, titulaire_ou_remplacant, joueur_id)', null);
        exit;
    }

    $poste = Poste::fromName($data['poste']);
    $tor   = TitulaireOuRemplacant::fromName($data['titulaire_ou_remplacant']);

    if ($poste === null || $tor === null) {
        deliver_response(400, 'Valeur invalide pour poste ou titulaire_ou_remplacant', null);
        exit;
    }

    $result = $ctrl->modifierParticipation(
        $id,
        $poste,
        $tor,
        (int) $data['joueur_id']
    );

    if ($result) {
        deliver_response(200, 'Participation modifiée avec succès', null);
    } else {
        deliver_response(500, 'Erreur lors de la modification', null);
    }

// ─── DELETE /participations/{participationId} ────────────────────────────────
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

// ─── PUT /participations/{participationId}/performance ───────────────────────
// Évaluation de la performance après la rencontre
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
        deliver_response(400, 'Impossible de mettre à jour : la rencontre n\'est pas encore passée', null);
    }

// ─── DELETE /participations/{participationId}/performance ────────────────────
// Supprime l'évaluation de performance
} elseif ($method === 'DELETE' && $id !== null && $sousRoute === 'performance') {
    if ($role !== 'directeur') {
        deliver_response(403, 'Accès interdit', null);
        exit;
    }

    $result = $ctrl->supprimerLaPerformance($id);

    if ($result) {
        deliver_response(200, 'Performance supprimée', null);
    } else {
        deliver_response(400, 'Impossible de supprimer : la rencontre n\'est pas encore passée', null);
    }

// ─── Méthode non supportée ───────────────────────────────────────────────────
} else {
    deliver_response(405, 'Méthode ou route non autorisée', null);
}