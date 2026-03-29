<?php
use R301\Controleur\StatistiquesControleur;
use R301\Controleur\JoueurControleur;

$ctrlStats   = StatistiquesControleur::getInstance();
$ctrlJoueurs = JoueurControleur::getInstance();

$sousRessource = $segments[1] ?? null;
$idJoueur      = isset($segments[2]) && $segments[2] !== '' ? (int)$segments[2] : null;

if ($method !== 'GET') {
    deliver_response(405, 'Méthode non autorisée : les statistiques sont en lecture seule', null);
    exit;
}

// GET /statistiques/equipe
if ($sousRessource === 'equipe') {
    $statsObj = $ctrlStats->getStatistiquesEquipe();

    // On construit le tableau manuellement car StatistiquesEquipe n'implémente pas JsonSerializable
    // Et on protège contre la division par zéro si aucun match n'est joué
    $nbVictoires  = $statsObj->nbVictoires();
    $nbNuls       = $statsObj->nbNuls();
    $nbDefaites   = $statsObj->nbDefaites();
    $nbJoues      = $nbVictoires + $nbNuls + $nbDefaites;

    $result = [
        'nbVictoires'              => $nbVictoires,
        'nbNuls'                   => $nbNuls,
        'nbDefaites'               => $nbDefaites,
        'pourcentageDeVictoires'   => $nbJoues > 0 ? round($nbVictoires / $nbJoues * 100) : 0,
        'pourcentageDeNuls'        => $nbJoues > 0 ? round($nbNuls       / $nbJoues * 100) : 0,
        'pourcentageDeDefaites'    => $nbJoues > 0 ? round($nbDefaites   / $nbJoues * 100) : 0,
    ];

    deliver_response(200, 'Succès', $result);

// GET /statistiques/joueurs
} elseif ($sousRessource === 'joueurs' && $idJoueur === null) {
    $statsObj = $ctrlStats->getStatistiquesJoueurs();
    $joueurs  = $ctrlJoueurs->listerTousLesJoueurs();

    // On construit un tableau par joueur indexé par joueurId
    $result = [];
    foreach ($joueurs as $joueur) {
        $result[] = [
            'joueurId'                   => $joueur->getJoueurId(),
            'poste_le_plus_performant'   => $statsObj->posteLePlusPerformant($joueur)?->name,
            'nb_rencontres_consecutives' => $statsObj->nbRencontresConsecutivesADate($joueur),
            'nb_titularisations'         => $statsObj->nbTitularisations($joueur),
            'nb_remplacant'              => $statsObj->nbRemplacant($joueur),
            'moyenne_evaluations'        => $statsObj->moyenneDesEvaluations($joueur),
            'pourcentage_matchs_gagnes'  => $statsObj->pourcentageDeMatchsGagnes($joueur),
        ];
    }

    deliver_response(200, 'Succès', $result);

// GET /statistiques/joueurs/{id}
} elseif ($sousRessource === 'joueurs' && $idJoueur !== null) {
    $joueur = $ctrlJoueurs->getJoueurById($idJoueur);
    if ($joueur === null) {
        deliver_response(404, 'Joueur non trouvé', null);
        exit;
    }

    $statsObj = $ctrlStats->getStatistiquesJoueurs();

    $result = [
        'joueurId'                   => $joueur->getJoueurId(),
        'nom'                        => $joueur->getNom(),
        'prenom'                     => $joueur->getPrenom(),
        'poste_le_plus_performant'   => $statsObj->posteLePlusPerformant($joueur)?->name,
        'nb_rencontres_consecutives' => $statsObj->nbRencontresConsecutivesADate($joueur),
        'nb_titularisations'         => $statsObj->nbTitularisations($joueur),
        'nb_remplacant'              => $statsObj->nbRemplacant($joueur),
        'moyenne_evaluations'        => $statsObj->moyenneDesEvaluations($joueur),
        'pourcentage_matchs_gagnes'  => $statsObj->pourcentageDeMatchsGagnes($joueur),
    ];

    deliver_response(200, 'Succès', $result);

} else {
    deliver_response(404, 'Route statistiques inconnue. Utilisez /statistiques/equipe ou /statistiques/joueurs', null);
}
