<h1>Modifier une rencontre</h1>
<?php

use R301\API_client\ApiClient;
use R301\Vue\Component\Formulaire;

$token = $_SESSION['token'];

// CAS 1 : Soumission du formulaire (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST'
        && isset($_GET['id'], $_POST['dateHeure'], $_POST['equipeAdverse'], $_POST['adresse'], $_POST['lieu'])
) {
    // On envoie les modifications au backend via PUT /rencontres/{id}
    $reponse = ApiClient::put('/rencontres/' . (int)$_GET['id'], [
        'dateHeure'     => $_POST['dateHeure'],
        'equipeAdverse' => $_POST['equipeAdverse'],
        'adresse'       => $_POST['adresse'],
        'lieu'          => $_POST['lieu'],
    ], $token);

    if ($reponse['status'] === 200) {
        header('Location: ' . BASE_PATH . '/rencontre');
        exit;
    } else {
        $erreur = $reponse['data']['message'] ?? "Erreur lors de la modification de la rencontre";
    }

// CAS 2 : Affichage du formulaire pré-rempli (GET)
} else {
    if (!isset($_GET['id'])) {
        header("Location: " . BASE_PATH . "/rencontre");
        exit;
    }

    // On récupère les données actuelles de la rencontre pour pré-remplir le formulaire
    $reponse = ApiClient::get('/rencontres/' . (int)$_GET['id'], $token);
    if ($reponse['status'] !== 200) {
        header("Location: " . BASE_PATH . "/rencontre");
        exit;
    }
    $rencontre = $reponse['data'];
    $lieux = ['DOMICILE', 'EXTERIEUR'];

    $formulaire = new Formulaire(BASE_PATH . "/rencontre/modifier?id=" . $rencontre['rencontreId']);
    $formulaire->setDateTime("Date", "dateHeure", date("Y-m-d H:i"), $rencontre['dateEtHeure']); // pré-rempli
    $formulaire->setText("Equipe adverse", "equipeAdverse", "", $rencontre['equipeAdverse']);     // pré-rempli
    $formulaire->setText("Adresse", "adresse", "", $rencontre['adresse']);                        // pré-rempli
    $formulaire->setSelect("Lieu", $lieux, "lieu", $rencontre['lieu']);                           // lieu actuel sélectionné
    $formulaire->addButton("Submit", "update", "Valider", "Modifier");
    echo $formulaire;
}

if (isset($erreur)) {
    echo '<p style="color:red;">' . htmlspecialchars($erreur) . '</p>';
}
