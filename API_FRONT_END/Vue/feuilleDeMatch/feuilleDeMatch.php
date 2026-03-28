<?php

use R301\API_client\ApiClient;

$token = $_SESSION['token'];

if (!isset($_GET['id'])) {
    header("Location: " . BASE_PATH . "/rencontre");
    exit;
}

$rencontreId = (int)$_GET['id'];

// Récupérer la feuille de match
$repFeuille = ApiClient::get('/participations/' . $rencontreId . '/feuille', $token);
if ($repFeuille['status'] !== 200) {
    header("Location: " . BASE_PATH . "/rencontre");
    exit;
}
$feuilleDeMatch = $repFeuille['data']??[];

// Récupérer les joueurs sélectionnables
$repJoueurs = ApiClient::get('/joueurs', $token);
$joueursSelectionnables = $repJoueurs['data'] ?? [];

$postes = ['TOPLANE', 'JUNGLE', 'MIDLANE', 'ADCARRY', 'SUPPORT'];
$roles  = ['TITULAIRE', 'REMPLACANT'];
?>

<div style="display: flex; flex-direction: row; justify-content: space-between; align-items: center; padding-right: 30px">
    <h1>Feuille de Match</h1>
    <?php if($feuilleDeMatch['estComplete']): ?>
    <div class="etat-feuille-de-match feuille-de-match-complete">COMPLÈTE</div>
    <?php else: ?>
    <div class="etat-feuille-de-match feuille-de-match-incomplete">INCOMPLÈTE</div>
    <?php endif; ?>
</div>

<div class="container" style="display: flex; flex-direction: row; justify-content: space-between">
    <?php foreach ($roles as $role): ?>
    <table style="width: 49.5%">
        <caption><?php echo $role . 'S' ?></caption>
        <tr>
            <th style="width:15%">Poste</th>
            <th style="width:30%">Joueur</th>
            <th style="width:35%">Sélectionner un joueur</th>
            <th style="width:20%; min-width: 150px;"></th>
        </tr>

        <?php foreach ($postes as $poste):
            // Trouver le participant à ce poste/rôle dans la feuille
            $participant = null;
            foreach ($feuilleDeMatch['participants']??[] as $p) {
                if ($p['poste'] === $poste && $p['titulaireOuRemplacant'] === $role) {
                    $participant = $p;
                    break;
                }
            }

            // Construire la liste des joueurs sélectionnables pour le select
            $options = '<option value=""></option>';
            foreach ($joueursSelectionnables as $j) {
                $sel = ($participant && $participant['participant']['joueurId'] == $j['joueurId']) ? 'selected' : '';
                $options .= '<option value="' . $j['joueurId'] . '" ' . $sel . '>'
                          . htmlspecialchars($j['nom'] . ' ' . $j['prenom']) . '</option>';
            }
            // Ajouter aussi le joueur déjà assigné s'il n'est pas dans la liste
            if ($participant) {
                $dejaDans = false;
                foreach ($joueursSelectionnables as $j) {
                    if ($j['joueurId'] == $participant['participant']['joueurId']) { $dejaDans = true; break; }
                }
                if (!$dejaDans) {
                    $options .= '<option value="' . $participant['participant']['joueurId'] . '" selected>'
                              . htmlspecialchars($participant['participant']['nom'] . ' ' . $participant['participant']['prenom'])
                              . '</option>';
                }
            }
        ?>
        <form action="<?= BASE_PATH ?>/feuilleDeMatch/modifier" method="post">
            <tr>
                <input type="hidden" name="participationId" value="<?php echo $participant ? $participant['participationId'] : '' ?>" />
                <input type="hidden" name="poste" value="<?php echo $poste ?>" />
                <input type="hidden" name="rencontreId" value="<?php echo $rencontreId ?>" />
                <input type="hidden" name="titulaireOuRemplacant" value="<?php echo $role ?>" />
                <td><?php echo $poste ?></td>
                <td><?php if($participant) echo htmlspecialchars($participant['participant']['nom'] . ' ' . $participant['participant']['prenom']) ?></td>
                <td>
                    <div class="row">
                        <div>
                            <select name="joueurId"><?php echo $options ?></select>
                        </div>
                    </div>
                </td>
                <td class="actions">
                    <?php if($participant): ?>
                    <button class="update" type="submit" name="action" value="update">Modifier</button>
                    <button class="delete" type="submit" name="action" value="delete" style="margin-left: 8px">Supprimer</button>
                    <?php else: ?>
                    <button class="create" type="submit" name="action" value="create">Assigner</button>
                    <?php endif; ?>
                </td>
            </tr>
        </form>
        <?php endforeach; ?>
    </table>
    <?php endforeach; ?>
</div>
