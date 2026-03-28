<?php

use R301\API_client\ApiClient;

$token = $_SESSION['token'];

// On récupère les statistiques depuis le backend via GET /statistiques
// Le backend renvoie un objet avec deux parties : equipe et joueurs
$repStats = ApiClient::get('/statistiques', $token);
$stats = $repStats['data'] ?? [];

// Stats globales de l'équipe (victoires, nuls, défaites, pourcentages)
$statsEquipe  = $stats['equipe']  ?? [];

// Stats par joueur, indexées par joueurId
// Ex: $statsJoueurs[5] = stats du joueur n°5
$statsJoueurs = $stats['joueurs'] ?? [];

// On récupère aussi la liste complète des joueurs pour avoir nom/prénom/statut
$repJoueurs = ApiClient::get('/joueurs', $token);
$joueurs = $repJoueurs['data'] ?? [];

?>

<!-- Grille de 6 cases : victoires, nuls, défaites + leurs pourcentages -->
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

<!-- Tableau des statistiques individuelles par joueur -->
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
            // On cherche les stats de ce joueur dans le tableau indexé par joueurId
            // Si pas de stats pour ce joueur → tableau vide (aucune participation)
            $sj = $statsJoueurs[$joueurId] ?? [];
        ?>
        <tr>
            <td><?php echo htmlspecialchars($joueur['nom'] . ' ' . $joueur['prenom']); ?></td>
            <td><?php echo htmlspecialchars($joueur['statut']); ?></td>
            <!-- ?? '' ou ?? 0 : valeur par défaut si la stat n'existe pas pour ce joueur -->
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
