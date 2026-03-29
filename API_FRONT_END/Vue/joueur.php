<?php

use R301\API_client\ApiClient;

// On récupère le token JWT stocké en session lors de la connexion
$token = $_SESSION['token'];

// On prépare les paramètres de recherche/filtre si l'utilisateur a utilisé le formulaire de filtre
$queryParams = [];
if (isset($_GET['recherche']) && $_GET['recherche'] !== '') {
    $queryParams['recherche'] = $_GET['recherche']; // filtre par nom/prénom
}
if (isset($_GET['statut']) && $_GET['statut'] !== '') {
    $queryParams['statut'] = $_GET['statut']; // filtre par statut (ACTIF, BLESSE...)
}

// On appelle le backend pour récupérer la liste des joueurs
// Si des queryParams existent, ils seront ajoutés à l'URL : /joueurs?statut=ACTIF
$reponse = ApiClient::get('/joueurs', $token, $queryParams);

// On récupère les données, tableau vide si problème
$joueurs = $reponse['data'] ?? [];

?>

<h1>Joueurs</h1>

<!-- Formulaire de recherche et filtre par statut -->
<div class="container">
    <form action="<?= BASE_PATH ?>/joueur" method="get">
        <div class="row">
            <div class="invCol-80">
                <!-- Champ de recherche : conserve la valeur saisie après filtrage -->
                <input type="search" name="recherche" placeholder="Rechercher"
                       <?= isset($_GET['recherche']) ? 'value="' . htmlspecialchars($_GET['recherche']) . '"' : '' ?>/>
            </div>
        </div>
        <div class="row">
            <div class="invCol-80">
                <!-- Liste déroulante pour filtrer par statut, conserve la valeur sélectionnée -->
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

<!-- Tableau d'affichage de tous les joueurs reçus du backend -->
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

        <!-- On boucle sur chaque joueur reçu du backend en JSON -->
        <?php foreach ($joueurs as $joueur) { ?>
            <tr>
                <!-- htmlspecialchars protège contre les injections HTML -->
                <!-- ?? '' évite une erreur si la clé n'existe pas -->
                <td><?php echo htmlspecialchars($joueur['numeroDeLicence'] ?? '') ?></td>
                <td><?php echo htmlspecialchars($joueur['nom'] ?? '') ?></td>
                <td><?php echo htmlspecialchars($joueur['prenom'] ?? '') ?></td>
                <td><?php echo htmlspecialchars($joueur['dateDeNaissance'] ?? '') ?></td>
                <td><?php echo htmlspecialchars($joueur['tailleEnCm'] ?? '') ?> cm</td>
                <td><?php echo htmlspecialchars($joueur['poidsEnKg'] ?? '') ?> kg</td>
                <td><?php echo htmlspecialchars($joueur['statut'] ?? '') ?></td>
                <td class="actions">
                    <!-- Bouton Modifier : envoie l'id du joueur en GET vers joueur/modifier -->
                    <form action="<?= BASE_PATH ?>/joueur/modifier" method="get">
                        <button class="update" type="submit" name="id" value="<?php echo $joueur['joueurId'] ?>">Modifier</button>
                    </form>
                    <!-- Bouton Supprimer : envoie l'id en POST vers joueur/supprimer avec confirmation -->
                    <form action="<?= BASE_PATH ?>/joueur/supprimer" method="post">
                        <button class="delete" type="submit" name="id" value="<?php echo $joueur['joueurId'] ?>"
                                onclick="return confirm('Voulez-vous vraiment supprimer ce joueur?')">Supprimer</button>
                    </form>
                    <!-- Bouton Commentaires : envoie l'id en GET vers joueur/commentaire -->
                    <form action="<?= BASE_PATH ?>/joueur/commentaire" method="get">
                        <button class="info" type="submit" name="id" value="<?php echo $joueur['joueurId'] ?>">Commentaires</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>
    <p><?php echo count($joueurs) ?> joueurs retournés</p>
</div>
