<h1>Ajouter un joueur</h1>
<?php

use R301\API_client\ApiClient;
use R301\Vue\Component\Formulaire;

$token = $_SESSION['token'];

// CAS 1 : L'utilisateur a soumis le formulaire (méthode POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['nom'], $_POST['prenom'], $_POST['numeroDeLicence'],
             $_POST['dateDeNaissance'], $_POST['tailleEnCm'], $_POST['poidsEnKg'], $_POST['statut'])
) {
    // On envoie les données au backend via POST /joueurs
    // Les clés du tableau doivent correspondre exactement à ce qu'attend le backend
    $reponse = ApiClient::post('/joueurs', [
        'numeroDeLicence' => $_POST['numeroDeLicence'],
        'nom'             => $_POST['nom'],
        'prenom'          => $_POST['prenom'],
        'dateDeNaissance' => $_POST['dateDeNaissance'],
        'tailleEnCm'      => (int) $_POST['tailleEnCm'], // (int) force le type entier
        'poidsEnKg'       => (int) $_POST['poidsEnKg'],
        'statut'          => $_POST['statut'],
    ], $token);

    // Si le backend répond 200 ou 201 (créé) → succès, on retourne à la liste
    if ($reponse['status'] === 201 || $reponse['status'] === 200) {
        header('Location: ' . BASE_PATH . '/joueur');
        exit;
    } else {
        // Sinon on récupère le message d'erreur renvoyé par le backend
        $erreur = $reponse['data']['message'] ?? "Erreur lors de la création du joueur";
    }

// CAS 2 : L'utilisateur arrive sur la page (méthode GET) → on affiche le formulaire vide
} else {
    // On construit le formulaire avec la classe Formulaire
    // Le paramètre est l'URL vers laquelle le formulaire sera soumis
    $formulaire = new Formulaire(BASE_PATH . "/joueur/ajouter");
    $formulaire->setText("Nom", "nom");
    $formulaire->setText("Prenom", "prenom");
    $formulaire->setText("Numéro de license", "numeroDeLicence", "00042"); // "00042" est le placeholder
    $formulaire->setDate("Date de naissance", "dateDeNaissance");
    $formulaire->setText("Taille (en cm)", "tailleEnCm");
    $formulaire->setText("Poids (en kg)", "poidsEnKg");
    $formulaire->setSelect("Statut", ["ACTIF", "BLESSE", "ABSENT", "SUSPENDU"], "statut");
    $formulaire->addButton("Submit", "create", "valider", "Valider");
    echo $formulaire; // déclenche __toString() qui retourne le HTML complet
}

// Si une erreur s'est produite lors de la soumission, on l'affiche en rouge
if (isset($erreur)) {
    echo '<p style="color:red;">' . htmlspecialchars($erreur) . '</p>';
}
