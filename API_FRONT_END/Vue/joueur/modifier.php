<h1>Modifier un joueur</h1>
<?php

use R301\API_client\ApiClient;
use R301\Vue\Component\Formulaire;

$token = $_SESSION['token'];

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_GET['id'], $_POST['nom'], $_POST['prenom'], $_POST['numeroDeLicence'],
             $_POST['dateDeNaissance'], $_POST['tailleEnCm'], $_POST['poidsEnKg'], $_POST['statut'])
) {
    // On envoie les noms de champs attendus par le backend
    $reponse = ApiClient::put('/joueurs/' . (int)$_GET['id'], [
        'numeroDeLicence'  => $_POST['numeroDeLicence'],
        'nom'             => $_POST['nom'],
        'prenom'          => $_POST['prenom'],
        'dateDeNaissance'  => $_POST['dateDeNaissance'],
        'tailleEnCm'          => (int) $_POST['tailleEnCm'],
        'poidsEnKg'           => (int) $_POST['poidsEnKg'],
        'statut'          => $_POST['statut'],
    ], $token);

    if ($reponse['status'] === 200) {
        header('Location: ' . BASE_PATH . '/joueur');
        exit;
    } else {
        $erreur = $reponse['data']['message'] ?? "Erreur lors de la modification du joueur";
    }
} else {
    if (!isset($_GET['id'])) {
        header("Location: " . BASE_PATH . "/joueur");
        exit;
    }

    $reponse = ApiClient::get('/joueurs/' . (int)$_GET['id'], $token);
    if ($reponse['status'] !== 200) {
        header("Location: " . BASE_PATH . "/joueur");
        exit;
    }
    $joueur = $reponse['data'];

    $formulaire = new Formulaire(BASE_PATH . "/joueur/modifier?id=" . $joueur['joueurId']);
    $formulaire->setText("Nom", "nom", "", $joueur['nom']);
    $formulaire->setText("Prenom", "prenom", "", $joueur['prenom']);
    $formulaire->setText("Numéro de license", "numeroDeLicence", "00042", $joueur['numero_licence']);
    $formulaire->setDate("Date de naissance", "dateDeNaissance", $joueur['date_naissance']);
    $formulaire->setText("Taille (en cm)", "tailleEnCm", "", $joueur['taille']);
    $formulaire->setText("Poids (en Kg)", "poidsEnKg", "", $joueur['poids']);
    $formulaire->setSelect("Statut", ["ACTIF", "BLESSE", "ABSENT", "SUSPENDU"], "statut", $joueur['statut']);
    $formulaire->addButton("Submit", "update", "modifier", "Modifier");
    echo $formulaire;
}

if (isset($erreur)) {
    echo '<p style="color:red;">' . htmlspecialchars($erreur) . '</p>';
}
