<?php

use R301\ApiClient\ApiClient;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["username"]) && isset($_POST["password"])) {

    $reponse = ApiClient::login(trim($_POST["username"]), trim($_POST["password"]));

    if ($reponse['status'] === 200 && isset($reponse['data']['token'])) {
        // On stocke le JWT en session
        $_SESSION['token'] = $reponse['data']['token'];
        $_SESSION['username'] = trim($_POST["username"]);
        header("Location: " . BASE_PATH . "/joueur");
        die();
    } else {
        $erreur = "Le nom d'utilisateur ou le mot de passe est incorrect";
    }
}
?>

<body>
    <div class="CentredContainer">
        <h1>Login</h1>
        <div class="container">
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
        <p><?php if (isset($erreur)) { echo $erreur; } ?></p>
    </div>
</body>
</html>
