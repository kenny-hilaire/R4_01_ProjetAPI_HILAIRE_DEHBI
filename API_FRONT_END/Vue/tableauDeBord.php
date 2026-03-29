<?php

use R301\API_client\ApiClient;

$token = $_SESSION['token'];


$repEquipe  = ApiClient::get('/statistiques/equipe', $token);
$repJoueurs_stats = ApiClient::get('/statistiques/joueurs', $token);

$statsEquipe  = $repEquipe['data']  ?? [];
$statsJoueurs = $repJoueurs_stats['data'] ?? [];

// On Récupére la liste des joueurs (nom/prénom/statut)
$repJoueurs = ApiClient::get('/joueurs', $token);
$joueurs = $repJoueurs['data'] ?? [];

// on Indexer les stats joueurs par joueurId pour pouvoir les retrouver
$statsJoueursParId = [];
foreach ($statsJoueurs as $sj) {
    $statsJoueursParId[$sj['joueur_id']] = $sj;
}
?>

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
        <?php foreach ($joueurs as $joueur):
            $joueurId = $joueur['joueurId'];
            $sj = $statsJoueursParId[$joueurId] ?? [];
        ?>
        <tr>
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