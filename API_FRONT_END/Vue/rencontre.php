<?php

use R301\API_client\ApiClient;

$token = $_SESSION['token'];

if ($_SERVER['REQUEST_METHOD'] === 'POST'
        && isset($_POST['action'], $_POST['rencontreId'])
) {
    switch($_POST['action']) {
        case "ouvrirFeuilleDeMatch":
            header('Location: ' . BASE_PATH . '/feuilleDeMatch/feuilleDeMatch?id=' . (int)$_POST['rencontreId']);
            die();
        case "ouvrirEvaluations":
            header('Location: ' . BASE_PATH . '/feuilleDeMatch/evaluation?id=' . (int)$_POST['rencontreId']);
            die();
        case "modifier":
            header('Location: ' . BASE_PATH . '/rencontre/modifier?id=' . (int)$_POST['rencontreId']);
            die();
        case "enregistrerResultat":
            if (isset($_POST['resultat'])) {
                $reponse = ApiClient::put('/rencontres/' . (int)$_POST['rencontreId'] . '/resultat', [
                    'resultat' => $_POST['resultat'],
                ], $token);
                if ($reponse['status'] !== 200) {
                    error_log("Erreur enregistrement résultat : " . json_encode($reponse));
                }
                header('Location: ' . BASE_PATH . '/rencontre');
                die();
            }
            break;
        case "supprimer":
            $reponse = ApiClient::delete('/rencontres/' . (int)$_POST['rencontreId'], $token);
            if ($reponse['status'] !== 200 && $reponse['status'] !== 204) {
                error_log("Erreur suppression rencontre : " . json_encode($reponse));
            }
            header('Location: ' . BASE_PATH . '/rencontre');
            die();
    }
}

$repRencontres = ApiClient::get('/rencontres', $token);
$rencontres = $repRencontres['data'] ?? [];

$resultats = ['VICTOIRE', 'DEFAITE', 'NUL'];
?>

<h1>Rencontres</h1>
<div class="overflow container">
    <table>
        <tr>
            <th style="width:10%">Date</th>
            <th style="width:10%">Equipe Adverse</th>
            <th style="width:20%">Adresse</th>
            <th style="width:8%">Lieu</th>
            <th style="width:8%">Résultat</th>
            <th style="width:20%; min-width: 200px;">Actions</th>
        </tr>
        <?php foreach ($rencontres as $rencontre): ?>
        <form action="<?= BASE_PATH ?>/rencontre" method="post">
            <tr>
                <input type="hidden" name="rencontreId" value="<?php echo $rencontre['rencontreId']; ?>" />
                <td><?php echo htmlspecialchars($rencontre['dateEtHeure']) ?></td>
                <td><?php echo htmlspecialchars($rencontre['equipeAdverse']) ?></td>
                <td><?php echo htmlspecialchars($rencontre['adresse']) ?></td>
                <td><?php echo htmlspecialchars($rencontre['lieu']) ?></td>
                <?php if ($rencontre['estPassee'] && $rencontre['resultat'] === null): ?>
                    <td>
                        <select name="resultat">
                            <option value=""></option>
                            <?php foreach ($resultats as $r): ?>
                            <option value="<?= $r ?>"><?= $r ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                <?php else: ?>
                    <td><?php echo htmlspecialchars($rencontre['resultat'] ?? '') ?></td>
                <?php endif; ?>
                <td class="actions">
                    <?php if (!$rencontre['estPassee']): ?>
                    <button name="action" value="ouvrirFeuilleDeMatch" class="info">Feuilles de match</button>
                    <button name="action" value="modifier" class="update">Modifier</button>
                    <button name="action" value="supprimer" class="delete">Supprimer</button>
                    <?php else: ?>
                    <button name="action" value="ouvrirEvaluations" class="info">Évaluations</button>
                    <?php if ($rencontre['estPassee'] && $rencontre['resultat'] === null): ?>
                    <button class="create" name="action" value="enregistrerResultat">Enregistrer résultat</button>
                    <?php endif; ?>
                    <?php endif; ?>
                </td>
            </tr>
        </form>
        <?php endforeach; ?>
    </table>
</div>
