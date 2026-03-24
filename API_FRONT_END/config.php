<?php

// =====================================================================
// CONFIGURATION DES URLs DES APIs
// Modifier ces constantes quand les URLs alwaysdata sont connues
// =====================================================================

// URL de l'API d'authentification (compte alwaysdata dédié à l'auth)
define('AUTH_API_URL', 'https://TON_COMPTE_AUTH.alwaysdata.net/API_auth');

// URL du backend (compte alwaysdata dédié au backend)
define('BACKEND_API_URL', 'https://TON_COMPTE_BACKEND.alwaysdata.net/API_BACK_END');

// Base path du frontend (sous-dossier sur alwaysdata)
define('BASE_PATH', '/API_FRONT_END');

// Clé secrète partagée pour vérifier les JWT (doit être identique à celle de l'API auth)
define('JWT_SECRET', 'secret');
