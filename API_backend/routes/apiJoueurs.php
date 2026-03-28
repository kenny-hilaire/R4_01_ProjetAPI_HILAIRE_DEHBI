<?php 
use R301\Controleur\JoueurControleur;

$ctrl = JoueurControleur::getInstance();

if ($method === 'GET' && $id == null) {
    // Filtre optionnel par recherche (nom/prénom) et statut
    $recherche = $_GET['recherche'] ?? '';
    $statut    = $_GET['statut'] ?? '';

    if ($recherche !== '' || $statut !== '') {
        $joueurs = $ctrl->rechercherLesJoueurs($recherche, $statut);
    } else {
        $joueurs = $ctrl->listerTousLesJoueurs();
    }
    deliver_response(200, 'Succes', $joueurs);

} elseif ($method == 'GET' && $id !== null) {
    $joueur = $ctrl->getJoueurById($id);
    if ($joueur == null) {
        deliver_response(404, 'Joueur non trouvé', null);
    } else {
        deliver_response(200, 'Succes', $joueur);
    }

} elseif ($method === 'POST') {
    if ($role !== 'directeur') {
        deliver_response(403, 'Acces interdit', null);
        exit;
    }
    $data = json_decode(file_get_contents('php://input'), true);
    // Ordre correct des paramètres : nom, prenom, numeroDeLicence, dateDeNaissance, tailleEnCm, poidsEnKg, statut
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
