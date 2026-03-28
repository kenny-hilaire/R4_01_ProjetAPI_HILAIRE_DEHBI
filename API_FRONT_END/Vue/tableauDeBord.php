<?php

use R301\API_client\ApiClient;

$token = $_SESSION['token'];

// Récupérer les stats équipe via GET /statistiques/equipe
$repStatsEquipe = ApiClient::get('/statistiques/equipe', $token);
$statsEquipe = $repStatsEquipe['data'] ?? [];

// Récupérer les stats joueurs via GET /statistiques/joueurs
$repStatsJoueurs = ApiClient::get('/statistiques/joueurs', $token);
$statsJoueursRaw = $repStatsJoueurs['data'] ?? [];

// Récupérer la liste des joueurs pour avoir nom/prénom/statut
$repJoueurs = ApiClient::get('/joueurs', $token);
$joueurs = $repJoueurs['data'] ?? [];

// Indexer les stats joueurs par joueurId pour un accès rapide
$statsJoueurs = [];
if (is_array($statsJoueursRaw)) {
    foreach ($statsJoueursRaw as $sj) {
        if (isset($sj['joueurId'])) {
            $statsJoueurs[$sj['joueurId']] = $sj;
        }
    }
}
?>

<!-- Grille de victoires, nuls, défaites + leurs pourcentages -->
<div class="TripleGrid">
    <div>
        <h1><?php echo $statsEquipe['nbVictoires'] ?? 0; ?></h1>
        <p> matchs gagnés</p>
    </div>
    <div>
        <h1><?php echo $statsEquipe['nbNuls'] ?? 0; ?></h1>
        <p> matchs nuls</p>
    </div>
    <div>
        <h1><?php echo $statsEquipe['nbDefaites'] ?? 0; ?></h1>
        <p> matchs perdus</p>
    </div>
    <div>
        <h1><?php echo $statsEquipe['pourcentageDeVictoires'] ?? 0; ?>%</h1>
        <p> de matchs gagnés</p>
    </div>
    <div>
        <h1><?php echo $statsEquipe['pourcentageDeNuls'] ?? 0; ?>%</h1>
        <p> de matchs nuls</p>
    </div>
    <div>
        <h1><?php echo $statsEquipe['pourcentageDeDefaites'] ?? 0; ?>%</h1>
        <p> de matchs perdus</p>
    </div>
</div>

<!-- Tableau des statistiques individuelles par joueur  -->
<div class="overflow">
    <table>
        <tr>
            <th style="width:15%;">Joueur</th>
            <th style="width:7%;">Statut</th>
            <th style="width:7%;">Poste le plus performant</th>
            <th style="width:7%;">Nombre de matchs consécutifs</th>
            <th style="width:7%;">Nombre titularisations</th>
            <th style="width:7%;">Nombre remplaçants</th>
            <th style="width:7%;">Moyenne évaluations</th>
            <th style="width:7%;">Pourcentage gagnés</th>
        </tr>

        <!-- On cherche les stats de ce joueur dans le tableau indexé par joueurId
        Si pas de stats pour ce joueur : tableau vide (aucune participation) -->
        <?php foreach ($joueurs as $joueur):
            $joueurId = $joueur['joueurId'];
            $sj = $statsJoueurs[$joueurId] ?? [];
        ?>
        <tr>
            <!-- ?? '' ou ?? 0 : valeur par défaut si la stat n'existe pas pour ce joueur -->
            <td><?php echo htmlspecialchars($joueur['nom'] . ' ' . $joueur['prenom']); ?></td>
            <td><?php echo htmlspecialchars($joueur['statut']); ?></td>
            <td><?php echo htmlspecialchars($sj['poste_le_plus_performant'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($sj['nb_rencontres_consecutives'] ?? 0); ?></td>
            <td><?php echo htmlspecialchars($sj['nb_titularisations'] ?? 0); ?></td>
            <td><?php echo htmlspecialchars($sj['nb_remplacant'] ?? 0); ?></td>
            <td><?php echo htmlspecialchars($sj['moyenne_evaluations'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($sj['pourcentage_matchs_gagnes'] ?? 0); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
)u