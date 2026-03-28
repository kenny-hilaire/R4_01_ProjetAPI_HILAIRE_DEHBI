<?php
use R301\Controleur\RencontreControleur;
use R301\Modele\Rencontre\RencontreLieu;

$ctrl = RencontreControleur::getInstance();

// GET /rencontres
if ($method === 'GET' && $id === null) {
    $rencontres = $ctrl->listerToutesLesRencontres();
    deliver_response(200, 'Succès', $rencontres);

// GET /rencontres/{id}
} elseif ($method === 'GET' && $id !== null) {
    $rencontre = $ctrl->getRenconterById($id);
    if ($rencontre === null) {
        deliver_response(404, 'Rencontre non trouvée', null);
    } else {
        deliver_response(200, 'Succès', $rencontre);
    }

// POST /rencontres
} elseif ($method === 'POST') {
    if ($role !== 'directeur') {
        deliver_response(403, 'Accès interdit', null);
        exit;
    }
    $data = json_decode(file_get_contents('php://input'), true);

    // Le frontend envoie dateHeure, equipeAdverse, adresse, lieu
    if (!isset($data['dateHeure'], $data['equipeAdverse'], $data['adresse'], $data['lieu'])) {
        deliver_response(400, 'Données manquantes (dateHeure, equipeAdverse, adresse, lieu)', null);
        exit;
    }

    $lieu = RencontreLieu::fromName($data['lieu']);
    if ($lieu === null) {
        deliver_response(400, 'Valeur invalide pour lieu. Valeurs acceptées : DOMICILE, EXTERIEUR', null);
        exit;
    }

    $result = $ctrl->ajouterRencontre(
        new DateTime($data['dateHeure']),
        $data['equipeAdverse'],
        $data['adresse'],
        $lieu
    );

    if ($result) {
        deliver_response(201, 'Rencontre créée', null);
    } else {
        deliver_response(400, 'Impossible de créer : la date est déjà passée', null);
    }

// PUT /rencontres/{id}  ou  PUT /rencontres/{id}/resultat
} elseif ($method === 'PUT' && $id !== null) {
    if ($role !== 'directeur') {
        deliver_response(403, 'Accès interdit', null);
        exit;
    }

    $sousRoute = $segments[2] ?? null;

    // PUT /rencontres/{id}/resultat
    if ($sousRoute === 'resultat') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['resultat'])) {
            deliver_response(400, 'Donnée manquante (resultat)', null);
            exit;
        }
        $result = $ctrl->enregistrerResultat($id, $data['resultat']);
        if ($result) {
            deliver_response(200, 'Résultat enregistré', null);
        } else {
            deliver_response(400, 'Impossible : la rencontre n\'est pas encore passée', null);
        }

    // PUT /rencontres/{id}  →  modifier la rencontre
    } else {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['dateHeure'], $data['equipeAdverse'], $data['adresse'], $data['lieu'])) {
            deliver_response(400, 'Données manquantes (dateHeure, equipeAdverse, adresse, lieu)', null);
            exit;
        }
        $lieu = RencontreLieu::fromName($data['lieu']);
        if ($lieu === null) {
            deliver_response(400, 'Valeur invalide pour lieu', null);
            exit;
        }
        $result = $ctrl->modifierRencontre(
            $id,
            new DateTime($data['dateHeure']),
            $data['equipeAdverse'],
            $data['adresse'],
            $lieu
        );
        if ($result) {
            deliver_response(200, 'Rencontre modifiée avec succès', null);
        } else {
            deliver_response(400, 'Impossible de modifier : rencontre déjà passée ou date invalide', null);
        }
    }

// DELETE /rencontres/{id}
} elseif ($method === 'DELETE' && $id !== null) {
    if ($role !== 'directeur') {
        deliver_response(403, 'Accès interdit', null);
        exit;
    }
    $result = $ctrl->supprimerRencontre($id);
    if ($result) {
        deliver_response(200, 'Rencontre supprimée', null);
    } else {
        deliver_response(400, 'Impossible de supprimer', null);
    }

} else {
    deliver_response(405, 'Méthode ou route non autorisée', null);
}
