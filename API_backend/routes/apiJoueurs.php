<?php 
use R301\Controleur\JoueurControleur;
use R301\Controleur\CommentaireControleur;

$ctrl             = JoueurControleur::getInstance();
$ctrlCommentaire  = CommentaireControleur::getInstance();

// Détecter les sous-routes : /joueurs/{id}/commentaires ou /commentaires/{id}
$sousRoute = $segments[2] ?? null; // ex: 'commentaires'

// ─── GET /joueurs ─────────────────────────────────────────────────────────────
if ($method === 'GET' && $id === null && $sousRoute === null) {
    $recherche = $_GET['recherche'] ?? '';
    $statut    = $_GET['statut'] ?? '';

    if ($recherche !== '' || $statut !== '') {
        $joueurs = $ctrl->rechercherLesJoueurs($recherche, $statut);
    } else {
        $joueurs = $ctrl->listerTousLesJoueurs();
    }
    deliver_response(200, 'Succes', $joueurs);

// ─── GET /joueurs/{id} ────────────────────────────────────────────────────────
} elseif ($method === 'GET' && $id !== null && $sousRoute === null) {
    $joueur = $ctrl->getJoueurById($id);
    if ($joueur === null) {
        deliver_response(404, 'Joueur non trouvé', null);
    } else {
        deliver_response(200, 'Succes', $joueur);
    }

// ─── GET /joueurs/{id}/commentaires ───────────────────────────────────────────
} elseif ($method === 'GET' && $id !== null && $sousRoute === 'commentaires') {
    $joueur = $ctrl->getJoueurById($id);
    if ($joueur === null) {
        deliver_response(404, 'Joueur non trouvé', null);
        exit;
    }
    $commentaires = $ctrlCommentaire->listerLesCommentairesDuJoueur($joueur);
    // Sérialiser manuellement car Commentaire n'implémente pas JsonSerializable
    $result = array_map(function($c) {
        return [
            'commentaireId' => $c->getCommentaireId(),
            'contenu'       => $c->getContenu(),
            'date'          => $c->getDate()->format('Y-m-d H:i:s'),
        ];
    }, $commentaires);
    deliver_response(200, 'Succès', $result);

// ─── POST /joueurs/{id}/commentaires ──────────────────────────────────────────
} elseif ($method === 'POST' && $id !== null && $sousRoute === 'commentaires') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['contenu'])) {
        deliver_response(400, 'Donnée manquante (contenu)', null);
        exit;
    }
    $result = $ctrlCommentaire->ajouterCommentaire($data['contenu'], $id);
    if ($result) {
        deliver_response(201, 'Commentaire ajouté', null);
    } else {
        deliver_response(500, 'Erreur lors de l\'ajout du commentaire', null);
    }

// ─── POST /joueurs ────────────────────────────────────────────────────────────
} elseif ($method === 'POST' && $id === null) {
    if ($role !== 'directeur') {
        deliver_response(403, 'Acces interdit', null);
        exit;
    }
    $data = json_decode(file_get_contents('php://input'), true);
    $result = $ctrl->ajouterJoueur(
        $data['nom'],
        $data['prenom'],
        $data['numeroDeLicence'],
        new DateTime($data['dateDeNaissance']),
        (int) $data['tailleEnCm'],
        (int) $data['poidsEnKg'],
        $data['statut']
    );
    deliver_response(201, 'Joueur créé', $result);

// ─── PUT /joueurs/{id} ────────────────────────────────────────────────────────
} elseif ($method === 'PUT' && $id !== null) {
    if ($role !== 'directeur') {
        deliver_response(403, 'Accès interdit', null);
        exit;
    }
    $data = json_decode(file_get_contents('php://input'), true);
    $result = $ctrl->modifierJoueur(
        $id,
        $data['nom'],
        $data['prenom'],
        $data['numeroDeLicence'],
        new DateTime($data['dateDeNaissance']),
        (int) $data['tailleEnCm'],
        (int) $data['poidsEnKg'],
        $data['statut']
    );
    deliver_response(200, 'Joueur modifié avec succès', $result);

// ─── DELETE /joueurs/{id} ─────────────────────────────────────────────────────
} elseif ($method === 'DELETE' && $id !== null) {
    if ($role !== 'directeur') {
        deliver_response(403, 'Accès interdit', null);
        exit;
    }
    $result = $ctrl->supprimerJoueur($id);
    deliver_response(200, 'Joueur supprimé', $result);

} else {
    deliver_response(405, 'Méthode non autorisée', null);
}
