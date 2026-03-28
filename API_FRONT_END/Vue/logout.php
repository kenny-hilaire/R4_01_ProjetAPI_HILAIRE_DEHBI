<?php
// On détruit complètement la session PHP
// Ça efface le token JWT et le username stockés dans $_SESSION
// L'utilisateur devra se reconnecter pour accéder au site
session_destroy();

// On redirige vers la page de login
header('Location: ' . BASE_PATH . '/login');
exit;
