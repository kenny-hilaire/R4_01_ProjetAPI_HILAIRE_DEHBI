<h1>Ajouter un joueur</h1>
<?php

use R301\API_client\ApiClient;
use R301\Vue\Component\Formulaire;

$token = $_SESSION['token'];

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['nom'], $_POST['prenom'], $_POST['numeroDeLicence'],
             $_POST['dateDeNaissance'], $_POST['tailleEnCm'], $_POST['poidsEnKg'], $_POST['statut'])
) {
    // On envoie les noms de champs attendus par le backend
    $reponse = ApiClient::post('/joueurs', [
        'numero_licence'  => $_POST['numeroDeLicence'],
        'nom'             => $_POST['nom'],
        'prenom'          => $_POST['prenom'],
        'date_naissance'  => $_POST['dateDeNaissance'],
        'taille'          => (int) $_POST['tailleEnCm'],
        'poids'           => (int) $_POST['poidsEnKg'],
        'statut'          => $_POST['statut'],
    ], $token);

    if ($reponse['status'] === 201 || $reponse['status'] === 200) {
        header('Location: ' . BASE_PATH . '/joueur');
        exit;
    } else {
        $erreur = $reponse['data']['message'] ?? "Erreur lors de la création du joueur";
    }
} else {
    $formulaire = new Formulaire(BASE_PATH . "/joueur/ajouter");
    $formulaire->setText("Nom", "nom");
    $formulaire->setText("Prenom", "prenom");
    $formulaire->setText("Numéro de license", "numeroDeLicence", "00042");
    $formulaire->setDate("Date de naissance", "dateDeNaissance");
    $formulaire->setText("Taille (en cm)", "tailleEnCm");
    $formulaire->setText("Poids (en kg)", "poidsEnKg");
    $formulaire->setSelect("Statut", ["ACTIF", "BLESSE", "ABSENT", "SUSPENDU"], "statut");
    $formulaire->addButton("Submit", "create", "valider", "Valider");
    echo $formulaire;
}

if (isset($erreur)) {
    echo '<p style="color:red;">' . htmlspecialchars($erreur) . '</p>';
}
