<h1>Ajouter une rencontre</h1>
<?php

use R301\API_client\ApiClient;
use R301\Vue\Component\Formulaire;

$token = $_SESSION['token'];

if ($_SERVER['REQUEST_METHOD'] === 'POST'
        && isset($_POST['dateHeure'], $_POST['equipeAdverse'], $_POST['adresse'], $_POST['lieu'])
) {
    $reponse = ApiClient::post('/rencontres', [
        'date_heure'     => $_POST['dateHeure'],
        'equipe_adverse' => $_POST['equipeAdverse'],
        'adresse'       => $_POST['adresse'],
        'lieu'          => $_POST['lieu'],
    ], $token);

    if ($reponse['status'] === 201 || $reponse['status'] === 200) {
        header('Location: ' . BASE_PATH . '/rencontre');
        exit;
    } else {
        $erreur = $reponse['data']['message'] ?? "Erreur lors de la création de la rencontre";
    }
} else {
    $lieux = ['DOMICILE', 'EXTERIEUR'];
    $formulaire = new Formulaire(BASE_PATH . "/rencontre/ajouter");
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
