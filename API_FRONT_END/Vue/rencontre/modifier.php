<h1>Modifier une rencontre</h1>
<?php

use R301\API_client\ApiClient;
use R301\Vue\Component\Formulaire;

$token = $_SESSION['token'];

if ($_SERVER['REQUEST_METHOD'] === 'POST'
        && isset($_GET['id'], $_POST['dateHeure'], $_POST['equipeAdverse'], $_POST['adresse'], $_POST['lieu'])
) {
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
} else {
    if (!isset($_GET['id'])) {
        header("Location: " . BASE_PATH . "/rencontre");
        exit;
    }

    $reponse = ApiClient::get('/rencontres/' . (int)$_GET['id'], $token);
    if ($reponse['status'] !== 200) {
        header("Location: " . BASE_PATH . "/rencontre");
        exit;
    }
    $rencontre = $reponse['data'];
    $lieux = ['DOMICILE', 'EXTERIEUR'];

    $formulaire = new Formulaire(BASE_PATH . "/rencontre/modifier?id=" . $rencontre['rencontreId']);
    $formulaire->setDateTime("Date", "dateHeure", date("Y-m-d H:i"), $rencontre['dateEtHeure']);
    $formulaire->setText("Equipe adverse", "equipeAdverse", "", $rencontre['equipeAdverse']);
    $formulaire->setText("Adresse", "adresse", "", $rencontre['adresse']);
    $formulaire->setSelect("Lieu", $lieux, "lieu", $rencontre['lieu']);
    $formulaire->addButton("Submit", "update", "Valider", "Modifier");
    echo $formulaire;
}

if (isset($erreur)) {
    echo '<p style="color:red;">' . htmlspecialchars($erreur) . '</p>';
}
