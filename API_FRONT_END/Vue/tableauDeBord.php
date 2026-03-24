<?php

use R301\ApiClient\ApiClient;

$token = $_SESSION['token'];

// Récupérer les statistiques équipe + joueurs
$repStats = ApiClient::get('/statistiques', $token);
$stats = $repStats['data'] ?? [];

$statsEquipe  = $stats['equipe']  ?? [];
$statsJoueurs = $stats['joueurs'] ?? [];

// Récupérer la liste des joueurs (pour avoir nom/prénom/statut)
$repJoueurs = ApiClient::get('/joueurs', $token);
$joueurs = $repJoueurs['data'] ?? [];

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
            // Chercher les stats de ce joueur dans statsJoueurs (indexé par joueurId)
            $joueurId = $joueur['joueurId'];
            $sj = $statsJoueurs[$joueurId] ?? [];
        ?>
        <tr>
            <td><?php echo htmlspecialchars($joueur['nom'] . ' ' . $joueur['prenom']); ?></td>
            <td><?php echo htmlspecialchars($joueur['statut']); ?></td>
            <td><?php echo htmlspecialchars($sj['posteLePlusPerformant'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($sj['nbRencontresConsecutives'] ?? 0); ?></td>
            <td><?php echo htmlspecialchars($sj['nbTitularisations'] ?? 0); ?></td>
            <td><?php echo htmlspecialchars($sj['nbRemplacant'] ?? 0); ?></td>
            <td><?php echo htmlspecialchars($sj['moyenneDesEvaluations'] ?? ''); ?></td>
            <td><?php echo htmlspecialchars($sj['pourcentageDeMatchsGagnes'] ?? 0); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
