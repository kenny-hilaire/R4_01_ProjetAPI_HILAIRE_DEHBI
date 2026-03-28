<?php
use R301\Controleur\StatistiquesControleur;
use R301\Controleur\JoueurControleur;

$ctrlStats   = StatistiquesControleur::getInstance();
$ctrlJoueurs = JoueurControleur::getInstance();

// Le 2e segment est l'identifiant de sous-ressource (equipe, joueurs)
// Le 3e segment est un éventuel id de joueur
// Rappel : $segments = ['statistiques', sousRessource, idJoueur]
$sousRessource = $segments[1] ?? null;  // 'equipe' ou 'joueurs'
$idJoueur      = isset($segments[2]) && $segments[2] !== '' ? (int)$segments[2] : null;

// Les statistiques sont en lecture seule — on refuse toute autre méthode
if ($method !== 'GET') {
    deliver_response(405, 'Méthode non autorisée : les statistiques sont en lecture seule', null);
    exit;
}

// ------ GET /statistiques/equipe ------------------------------------------------------------------------------------------------─
if ($sousRessource === 'equipe') {
    $stats = $ctrlStats->getStatistiquesEquipe();
    deliver_response(200, 'Succès', $stats);

// ------ GET /statistiques/joueurs ------------------------------------------------------------------------------------------------
} elseif ($sousRessource === 'joueurs' && $idJoueur === null) {
    $stats = $ctrlStats->getStatistiquesJoueurs();
    deliver_response(200, 'Succès', $stats);
// -----------------------------------------------------------------------------
// ---- GET /statistiques/joueurs/{id} ------------------------------------------------------------------------------------
} elseif ($sousRessource === 'joueurs' && $idJoueur !== null) {
    $joueur = $ctrlJoueurs->getJoueurById($idJoueur);

    if ($joueur === null) {
        deliver_response(404, 'Joueur non trouvé', null);
        exit;
    }

    $statsJoueurs = $ctrlStats->getStatistiquesJoueurs();

    // On construit la réponse pour un joueur précis
    $result = [
        'joueur_id'                         => $joueur->getJoueurId(),
        'nom'                              => $joueur->getNom(),
        'prenom'                            => $joueur->getPrenom(),
        'nb_titularisations'                => $statsJoueurs->nbTitularisations($joueur),
        'nb_remplacant'                     => $statsJoueurs->nbRemplacant($joueur),
        'moyenne_evaluations'               => $statsJoueurs->moyenneDesEvaluations($joueur),
        'pourcentage_matchs_gagnes'         => $statsJoueurs->pourcentageDeMatchsGagnes($joueur),
        'nb_rencontres_consecutives'        => $statsJoueurs->nbRencontresConsecutivesADate($joueur),
        'poste_le_plus_performant'          => $statsJoueurs->posteLePlusPerformant($joueur)?->name,
    ];

    deliver_response(200, 'Succès', $result);

// ------ Route inconnue ------------------------------------------------------------------------------------------------------------------──
} else {
    deliver_response(404, 'Route statistiques inconnue. Utilisez /statistiques/equipe ou /statistiques/joueurs[/{id}]', null);
}