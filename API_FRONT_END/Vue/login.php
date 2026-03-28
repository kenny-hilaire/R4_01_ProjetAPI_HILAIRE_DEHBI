<?php

use R301\API_client\ApiClient;

// On vérifie si l'utilisateur vient de soumettre le formulaire de login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["username"]) && isset($_POST["password"])) {

    // On appelle l'API auth pour vérifier les identifiants
    // ApiClient::login envoie un POST à AUTH_API_URL/login avec login + password
    $reponse = ApiClient::login(trim($_POST["username"]), trim($_POST["password"]));

    // Si l'API répond 200 et renvoie un token → connexion réussie
    if ($reponse['status'] === 200 && isset($reponse['data'])) {

        // On stocke le JWT dans la session PHP
        // Ce token sera envoyé dans toutes les prochaines requêtes vers le backend
        $_SESSION['token'] = $reponse['data'];

        // On stocke aussi le nom d'utilisateur (pour l'afficher si besoin)
        $_SESSION['username'] = trim($_POST["username"]);

        // On redirige vers la liste des joueurs
        header("Location: " . BASE_PATH . "/joueur");
        die();
    } else {
        // Mauvais identifiants → on prépare un message d'erreur
        $erreur = "Le nom d'utilisateur ou le mot de passe est incorrect";
    }
}
?>

<body>
    <div class="CentredContainer">
        <h1>Login</h1>
        <div class="container">
            <!-- Le formulaire envoie les données en POST vers /login -->
            <form action="<?= BASE_PATH ?>/login" method="post">
                <div class="row">
                    <div class="col-20">
                        <label for="username">Username : </label>
                    </div>
                    <div class="col-80">
                        <input type="text" id="username" name="username"/><br>
                    </div>
                </div>
                <div class="row">
                    <div class="col-20">
                        <label for="password">Password : </label>
                    </div>
                    <div class="col-80">
                        <input type="password" id="pass" name="password"/><br>
                    </div>
                </div>
                <div class="row">
                    <input type="submit" value="Login"/>
                </div>
            </form>
        </div>
        <!-- Si une erreur existe, on l'affiche sous le formulaire -->
        <p><?php if (isset($erreur)) { echo $erreur; } ?></p>
    </div>
</body>
</html>
