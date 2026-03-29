<?php

use R301\API_client\ApiClient;

$token = $_SESSION['token'];

if ($_SERVER['REQUEST_METHOD'] === 'POST'
        && isset($_POST['action'], $_POST['participationId'], $_POST['rencontreId'], $_POST['performance'])
) {
    switch($_POST['action']) {
        case "update":
            $reponse = ApiClient::put('/participations/' . (int)$_POST['participationId'] . '/performance', [
                'performance' => $_POST['performance'],
            ], $token);
            if ($reponse['status'] !== 200) {
                error_log("Erreur mise à jour performance : " . json_encode($reponse));
            }
            break;
        case "delete":
            $reponse = ApiClient::delete('/participations/' . (int)$_POST['participationId'] . '/performance', $token);
            if ($reponse['status'] !== 200 && $reponse['status'] !== 204) {
                error_log("Erreur suppression performance : " . json_encode($reponse));
            }
            break;
    }
    header('Location: ' . BASE_PATH . '/feuilleDeMatch/evaluation?id=' . (int)$_POST['rencontreId']);
    die();
} else {
    if (!isset($_GET['id'])) {
        header("Location: " . BASE_PATH . "/rencontre");
        die();
    }

    $rencontreId = (int)$_GET['id'];

    // Correction : même route que feuilleDeMatch.php → /participations/{id}/feuille
    $repFeuille = ApiClient::get('/participations/' . $rencontreId . '/feuille', $token);
    if ($repFeuille['status'] !== 200) {
        header("Location: " . BASE_PATH . "/rencontre");
        die();
    }
    $feuilleDeMatch = $repFeuille['data'] ?? [];

    $postes = ['TOPLANE', 'JUNGLE', 'MIDLANE', 'ADCARRY', 'SUPPORT'];
    $roles  = ['TITULAIRE', 'REMPLACANT'];
    $performances = ['MAUVAISE', 'MOYENNE', 'BONNE', 'EXCELLENTE'];
?>

<div style="display: flex; flex-direction: row; justify-content: space-between; align-items: center; padding-right: 30px">
    <h1>Évaluations</h1>
    <?php if($feuilleDeMatch['estEvaluee'] ?? false): ?>
        <div class="etat-feuille-de-match feuille-de-match-complete">TERMINÉES</div>
    <?php else: ?>
        <div class="etat-feuille-de-match feuille-de-match-incomplete">INCOMPLÈTES</div>
    <?php endif; ?>
</div>

<div class="container" style="display: flex; flex-direction: row; justify-content: space-between">
    <?php foreach ($roles as $role): ?>
        <table style="width: 49.5%">
            <caption><?php echo $role . 'S' ?></caption>
            <tr>
                <th style="width:15%">Poste</th>
                <th style="width:25%">Joueur</th>
                <th style="width:15%">Performance</th>
                <th style="width:20%">Mettre à jour la performance</th>
                <th style="width:25%; min-width: 150px;"></th>
            </tr>

            <?php foreach ($postes as $poste):
                $participant = null;
                // Le modèle retourne 'participants' (pas 'participations')
                foreach ($feuilleDeMatch['participants'] ?? [] as $p) {
                    if ($p['poste'] === $poste && $p['titulaireOuRemplacant'] === $role) {
                        $participant = $p;
                        break;
                    }
                }
                $selectedPerf = $participant['performance'] ?? null;

                $optionsPerf = '<option value=""></option>';
                foreach ($performances as $perf) {
                    $sel = ($selectedPerf === $perf) ? 'selected' : '';
                    $optionsPerf .= '<option value="' . $perf . '" ' . $sel . '>' . $perf . '</option>';
                }

                // Le modèle retourne 'participant' (objet joueur) et 'rencontre' (objet rencontre)
                $rencontreIdParticipant = $participant['rencontre']['rencontreId'] ?? $rencontreId;
            ?>
            <form action="<?= BASE_PATH ?>/feuilleDeMatch/evaluation" method="post">
                <tr>
                    <input type="hidden" name="rencontreId" value="<?php echo $rencontreIdParticipant; ?>" />
                    <input type="hidden" name="participationId" value="<?php echo $participant ? $participant['participationId'] : '' ?>" />
                    <td><?php echo $poste ?></td>
                    <td><?php if($participant) echo htmlspecialchars($participant['participant']['nom'] . ' ' . $participant['participant']['prenom']) ?></td>
                    <td><?php if($selectedPerf) echo htmlspecialchars($selectedPerf) ?></td>
                    <td>
                        <div class="row"><div>
                            <select name="performance"><?php echo $optionsPerf ?></select>
                        </div></div>
                    </td>
                    <?php if($participant): ?>
                    <td class="actions">
                        <button class="update" type="submit" name="action" value="update">Mettre à jour</button>
                        <button class="delete" type="submit" name="action" value="delete" style="margin-left: 8px">Supprimer</button>
                    </td>
                    <?php else: ?>
                    <td></td>
                    <?php endif; ?>
                </tr>
            </form>
            <?php endforeach; ?>
        </table>
    <?php endforeach; ?>
</div>
<?php } ?>
