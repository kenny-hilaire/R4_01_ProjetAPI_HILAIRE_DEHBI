<?php

use R301\API_client\ApiClient;

$token = $_SESSION['token'];

// CAS 1 : L'utilisateur a cliqué sur un bouton d'action (POST)
// Tous les boutons du tableau renvoient un champ "action" et "rencontreId"
if ($_SERVER['REQUEST_METHOD'] === 'POST'
        && isset($_POST['action'], $_POST['rencontreId'])
) {
    switch($_POST['action']) {

        // Redirection vers la feuille de match de cette rencontre
        case "ouvrirFeuilleDeMatch":
            header('Location: ' . BASE_PATH . '/feuilleDeMatch/feuilleDeMatch?id=' . (int)$_POST['rencontreId']);
            die();

        // Redirection vers les évaluations de cette rencontre
        case "ouvrirEvaluations":
            header('Location: ' . BASE_PATH . '/feuilleDeMatch/evaluation?id=' . (int)$_POST['rencontreId']);
            die();

        // Redirection vers le formulaire de modification
        case "modifier":
            header('Location: ' . BASE_PATH . '/rencontre/modifier?id=' . (int)$_POST['rencontreId']);
            die();

        // Enregistrement du résultat via PUT /rencontres/{id}/resultat
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

        // Suppression via DELETE /rencontres/{id}
        case "supprimer":
            $reponse = ApiClient::delete('/rencontres/' . (int)$_POST['rencontreId'], $token);
            if ($reponse['status'] !== 200 && $reponse['status'] !== 204) {
                error_log("Erreur suppression rencontre : " . json_encode($reponse));
            }
            header('Location: ' . BASE_PATH . '/rencontre');
            die();
    }
}

// CAS 2 : Affichage de la liste des rencontres (GET)
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
        <!-- Chaque ligne est un formulaire pour envoyer les actions avec l'id de la rencontre -->
        <form action="<?= BASE_PATH ?>/rencontre" method="post">
            <tr>
                <!-- Id caché transmis avec chaque action -->
                <input type="hidden" name="rencontreId" value="<?php echo $rencontre['rencontreId']; ?>" />
                <td><?php echo htmlspecialchars($rencontre['dateEtHeure']) ?></td>
                <td><?php echo htmlspecialchars($rencontre['equipeAdverse']) ?></td>
                <td><?php echo htmlspecialchars($rencontre['adresse']) ?></td>
                <td><?php echo htmlspecialchars($rencontre['lieu']) ?></td>

                <!-- Si la rencontre est passée et sans résultat → on affiche un select pour saisir le résultat -->
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
                    <!-- Sinon on affiche juste le résultat texte -->
                    <td><?php echo htmlspecialchars($rencontre['resultat'] ?? '') ?></td>
                <?php endif; ?>

                <td class="actions">
                    <!-- Si la rencontre n'est pas encore passée → on peut gérer la feuille de match -->
                    <?php if (!$rencontre['estPassee']): ?>
                    <button name="action" value="ouvrirFeuilleDeMatch" class="info">Feuilles de match</button>
                    <button name="action" value="modifier" class="update">Modifier</button>
                    <button name="action" value="supprimer" class="delete">Supprimer</button>
                    <?php else: ?>
                    <!-- Si elle est passée → on peut voir les évaluations et enregistrer le résultat -->
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
