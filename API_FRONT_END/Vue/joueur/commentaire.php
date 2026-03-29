<?php

use R301\API_client\ApiClient;
use R301\Vue\Component\Formulaire;

$token = $_SESSION['token'];

// Si pas d'id dans l'URL → on ne sait pas de quel joueur afficher les commentaires
if (!isset($_GET['id'])) {
    header('Location: ' . BASE_PATH . '/joueur');
    die();
}

$joueurId = (int)$_GET['id'];

// On récupère les infos du joueur pour afficher son nom dans le titre
$repJoueur = ApiClient::get('/joueurs/' . $joueurId, $token);
if ($repJoueur['status'] !== 200) {
    header('Location: ' . BASE_PATH . '/joueur');
    die();
}
$joueur = $repJoueur['data'];
?>

<h1>Commentaires de <?php echo htmlspecialchars($joueur['nom'] . ' ' . $joueur['prenom']); ?></h1>

<?php
// Formulaire pour ajouter un nouveau commentaire
$form = new Formulaire(BASE_PATH . "/joueur/commentaire/ajouter");
$form->addTextArea("contenu");                          // zone de texte pour le commentaire
$form->addHiddenInput("joueurId", $joueurId);           // id caché pour savoir à quel joueur rattacher le commentaire
$form->addButton("submit", "create", "Publier le commentaire", "Publier le commentaire");
echo $form;

// On récupère tous les commentaires du joueur depuis le backend
$repCommentaires = ApiClient::get('/joueurs/' . $joueurId . '/commentaires', $token);
$commentaires = $repCommentaires['data'] ?? [];

// On trie les commentaires du plus récent au plus ancien
usort($commentaires, function ($a, $b) {
    return strtotime($b['date']) <=> strtotime($a['date']);
});
?>

<div class="container">
    <table>
        <tr>
            <th style="min-width: 100px; width: 1%">Date</th>
            <th style="width: 80%">Commentaire</th>
            <th style="width: 1%"></th>
        </tr>
        <?php foreach ($commentaires as $commentaire): ?>
        <form action="<?= BASE_PATH ?>/joueur/commentaire/supprimer" method="post">
            <!-- On passe l'id du commentaire et l'id du joueur en hidden pour la suppression -->
            <input type="hidden" name="commentaireId" value="<?php echo $commentaire['commentaireId']; ?>" />
            <input type="hidden" name="joueurId" value="<?php echo $joueurId; ?>" />
            <tr>
                <td><?php echo htmlspecialchars($commentaire['date']); ?></td>
                <td><?php echo htmlspecialchars($commentaire['contenu']); ?></td>
                <td class="actions">
                    <button class="delete" type="submit">Supprimer</button>
                </td>
            </tr>
        </form>
        <?php endforeach; ?>
    </table>
</div>
