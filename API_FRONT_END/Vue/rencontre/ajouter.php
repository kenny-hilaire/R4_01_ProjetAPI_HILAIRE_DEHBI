<h1>Ajouter une rencontre</h1>
<?php

use R301\API_client\ApiClient;
use R301\Vue\Component\Formulaire;

$token = $_SESSION['token'];

// CAS 1 : Soumission du formulaire (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST'
        && isset($_POST['dateHeure'], $_POST['equipeAdverse'], $_POST['adresse'], $_POST['lieu'])
) {
    // On envoie la nouvelle rencontre au backend via POST /rencontres
    $reponse = ApiClient::post('/rencontres', [
        'dateHeure'     => $_POST['dateHeure'],
        'equipeAdverse' => $_POST['equipeAdverse'],
        'adresse'       => $_POST['adresse'],
        'lieu'          => $_POST['lieu'],
    ], $token);

    if ($reponse['status'] === 201 || $reponse['status'] === 200) {
        header('Location: ' . BASE_PATH . '/rencontre');
        exit;
    } else {
        $erreur = $reponse['data']['message'] ?? "Erreur lors de la création de la rencontre";
    }

// CAS 2 : Affichage du formulaire vide (GET)
} else {
    $lieux = ['DOMICILE', 'EXTERIEUR'];
    $formulaire = new Formulaire(BASE_PATH . "/rencontre/ajouter");
    // date("Y-m-d H:i") = date/heure minimum (on ne peut pas créer une rencontre dans le passé)
    $formulaire->setDateTime("Date", "dateHeure", date("Y-m-d H:i"));
    $formulaire->setText("Equipe adverse", "equipeAdverse");
    $formulaire->setText("Adresse", "adresse");
    $formulaire->setSelect("Lieu", $lieux, "lieu");
    $formulaire->addButton("Submit", "create", "Valider", "Valider");
    echo $formulaire;
}

if (isset($erreur)) {
    echo '<p style="color:red;">' . htmlspecialchars($erreur) . '</p>';
}
