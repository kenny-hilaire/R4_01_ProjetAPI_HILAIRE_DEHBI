<h1>Modifier un joueur</h1>
<?php

use R301\API_client\ApiClient;
use R301\Vue\Component\Formulaire;

$token = $_SESSION['token'];

// CAS 1 : L'utilisateur a soumis le formulaire (méthode POST)
// L'id du joueur est dans l'URL (?id=5) et les nouvelles données dans $_POST
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_GET['id'], $_POST['nom'], $_POST['prenom'], $_POST['numeroDeLicence'],
             $_POST['dateDeNaissance'], $_POST['tailleEnCm'], $_POST['poidsEnKg'], $_POST['statut'])
) {
    // On envoie les modifications au backend via PUT /joueurs/{id}
    // PUT = modifier une ressource existante
    $reponse = ApiClient::put('/joueurs/' . (int)$_GET['id'], [
        'numeroDeLicence' => $_POST['numeroDeLicence'],
        'nom'             => $_POST['nom'],
        'prenom'          => $_POST['prenom'],
        'dateDeNaissance' => $_POST['dateDeNaissance'],
        'tailleEnCm'      => (int) $_POST['tailleEnCm'],
        'poidsEnKg'       => (int) $_POST['poidsEnKg'],
        'statut'          => $_POST['statut'],
    ], $token);

    // Si succès → on retourne à la liste des joueurs
    if ($reponse['status'] === 200) {
        header('Location: ' . BASE_PATH . '/joueur');
        exit;
    } else {
        $erreur = $reponse['data']['message'] ?? "Erreur lors de la modification du joueur";
    }

// CAS 2 : L'utilisateur arrive sur la page (méthode GET) → on affiche le formulaire pré-rempli
} else {
    // Si pas d'id dans l'URL → on ne sait pas quel joueur modifier → retour à la liste
    if (!isset($_GET['id'])) {
        header("Location: " . BASE_PATH . "/joueur");
        exit;
    }

    // On récupère les données actuelles du joueur depuis le backend
    // pour pré-remplir le formulaire avec ses infos existantes
    $reponse = ApiClient::get('/joueurs/' . (int)$_GET['id'], $token);
    if ($reponse['status'] !== 200) {
        // Si le joueur n'existe pas → retour à la liste
        header("Location: " . BASE_PATH . "/joueur");
        exit;
    }
    $joueur = $reponse['data']; // tableau avec les infos du joueur

    // On construit le formulaire pré-rempli avec les données actuelles du joueur
    // L'id est passé dans l'URL du formulaire pour savoir quel joueur modifier au moment du POST
    $formulaire = new Formulaire(BASE_PATH . "/joueur/modifier?id=" . $joueur['joueurId']);
    $formulaire->setText("Nom", "nom", "", $joueur['nom']);                             // pré-rempli
    $formulaire->setText("Prenom", "prenom", "", $joueur['prenom']);                    // pré-rempli
    $formulaire->setText("Numéro de license", "numeroDeLicence", "00042", $joueur['numeroDeLicence']); // pré-rempli
    $formulaire->setDate("Date de naissance", "dateDeNaissance", $joueur['dateDeNaissance']); // pré-rempli
    $formulaire->setText("Taille (en cm)", "tailleEnCm", "", $joueur['tailleEnCm']);   // pré-rempli
    $formulaire->setText("Poids (en Kg)", "poidsEnKg", "", $joueur['poidsEnKg']);      // pré-rempli
    $formulaire->setSelect("Statut", ["ACTIF", "BLESSE", "ABSENT", "SUSPENDU"], "statut", $joueur['statut']); // statut actuel sélectionné
    $formulaire->addButton("Submit", "update", "modifier", "Modifier");
    echo $formulaire;
}

// Affichage de l'erreur si la modification a échoué
if (isset($erreur)) {
    echo '<p style="color:red;">' . htmlspecialchars($erreur) . '</p>';
}
