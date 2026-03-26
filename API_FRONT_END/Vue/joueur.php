<?php

use R301\API_client\ApiClient;

$token = $_SESSION['token'];

$queryParams = [];
if (isset($_GET['recherche']) && $_GET['recherche'] !== '') {
    $queryParams['recherche'] = $_GET['recherche'];
}
if (isset($_GET['statut']) && $_GET['statut'] !== '') {
    $queryParams['statut'] = $_GET['statut'];
}

$reponse = ApiClient::get('/joueurs', $token, $queryParams);
$joueurs = $reponse['data'] ?? [];

?>

<h1>Joueurs</h1>
<div class="container">
    <form action="<?= BASE_PATH ?>/joueur" method="get">
        <div class="row">
            <div class="invCol-80">
                <input type="search" name="recherche" placeholder="Rechercher"
                       <?= isset($_GET['recherche']) ? 'value="' . htmlspecialchars($_GET['recherche']) . '"' : '' ?>/>
            </div>
        </div>
        <div class="row">
            <div class="invCol-80">
                <select name="statut" id="statut">
                    <option value="">Tous</option>
                    <option value="ACTIF"    <?= (isset($_GET['statut']) && $_GET['statut'] === "ACTIF")    ? 'selected' : '' ?>>Actif</option>
                    <option value="BLESSE"   <?= (isset($_GET['statut']) && $_GET['statut'] === "BLESSE")   ? 'selected' : '' ?>>Blessé</option>
                    <option value="ABSENT"   <?= (isset($_GET['statut']) && $_GET['statut'] === "ABSENT")   ? 'selected' : '' ?>>Absent</option>
                    <option value="SUSPENDU" <?= (isset($_GET['statut']) && $_GET['statut'] === "SUSPENDU") ? 'selected' : '' ?>>Suspendu</option>
                </select>
            </div>
            <div class="invCol-20">
                <input class="filter-button" type="submit" value="Filtrer">
            </div>
        </div>
    </form>
</div>

<div class="overflow container">
    <table style="width: 100%">
        <tr>
            <th style="width:8%">Numero Licence</th>
            <th style="width:12%">Nom</th>
            <th style="width:12%">Prenom</th>
            <th style="width:12%">Date de naissance</th>
            <th style="width:12%">Taille</th>
            <th style="width:12%">Poids</th>
            <th style="width:12%">Statut</th>
            <th style="width:20%; min-width: 370px;">Actions</th>
        </tr>

        <?php foreach ($joueurs as $joueur) { ?>
            <tr>
                <td><?php echo htmlspecialchars($joueur['numeroDeLicence'] ?? '') ?></td>
                <td><?php echo htmlspecialchars($joueur['nom'] ?? '') ?></td>
                <td><?php echo htmlspecialchars($joueur['prenom'] ?? '') ?></td>
                <td><?php echo htmlspecialchars($joueur['dateDeNaissance'] ?? '') ?></td>
                <td><?php echo htmlspecialchars($joueur['tailleEnCm'] ?? '') ?> cm</td>
                <td><?php echo htmlspecialchars($joueur['poidsEnKg'] ?? '') ?> kg</td>
                <td><?php echo htmlspecialchars($joueur['statut'] ?? '') ?></td>
                <td class="actions">
                    <form action="<?= BASE_PATH ?>/joueur/modifier" method="get">
                        <button class="update" type="submit" name="id" value="<?php echo $joueur['joueurId'] ?>">Modifier</button>
                    </form>
                    <form action="<?= BASE_PATH ?>/joueur/supprimer" method="post">
                        <button class="delete" type="submit" name="id" value="<?php echo $joueur['joueurId'] ?>"
                                onclick="return confirm('Voulez-vous vraiment supprimer ce joueur?')">Supprimer</button>
                    </form>
                    <form action="<?= BASE_PATH ?>/joueur/commentaire" method="get">
                        <button class="info" type="submit" name="id" value="<?php echo $joueur['joueurId'] ?>">Commentaires</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>
    <p><?php echo count($joueurs) ?> joueurs retournés</p>
</div>
