    <?php

namespace R301\ApiClient;

/**
 * Classe utilitaire pour appeler les APIs backend via cURL.
 * Toutes les requêtes transmettent automatiquement le JWT stocké en session.
 */
class ApiClient {

    /**
     * Effectue une requête GET vers le backend.
     * @param string $endpoint  Ex: '/joueurs' ou '/joueurs/5'
     * @param string|null $token  JWT Bearer token
     * @param array $queryParams  Paramètres GET optionnels
     * @return array  ['status' => int, 'data' => mixed]
     */
    public static function get(string $endpoint, ?string $token = null, array $queryParams = []): array {
        $url = BACKEND_API_URL . $endpoint;
        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }
        return self::request('GET', $url, null, $token);
    }

    /**
     * Effectue une requête POST vers le backend.
     */
    public static function post(string $endpoint, array $body, ?string $token = null): array {
        $url = BACKEND_API_URL . $endpoint;
        return self::request('POST', $url, $body, $token);
    }

    /**
     * Effectue une requête PUT vers le backend.
     */
    public static function put(string $endpoint, array $body, ?string $token = null): array {
        $url = BACKEND_API_URL . $endpoint;
        return self::request('PUT', $url, $body, $token);
    }

    /**
     * Effectue une requête DELETE vers le backend.
     */
    public static function delete(string $endpoint, ?string $token = null): array {
        $url = BACKEND_API_URL . $endpoint;
        return self::request('DELETE', $url, null, $token);
    }

    /**
     * Appelle l'API d'authentification pour se connecter.
     * @return array ['status' => int, 'data' => ['token' => '...']]
     */
    public static function login(string $login, string $password): array {
        $url = AUTH_API_URL . '/login';
        return self::request('POST', $url, ['login' => $login, 'password' => $password], null);
    }

    /**
     * Méthode centrale : exécute la requête cURL.
     */
    private static function request(string $method, string $url, ?array $body, ?string $token): array {
        $ch = curl_init();

        $headers = ['Content-Type: application/json', 'Accept: application/json'];
        if ($token !== null) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        switch ($method) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
                break;
            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
                break;
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }

        $responseBody = curl_exec($ch);
        $httpStatus   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError    = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            error_log("ApiClient cURL error [$method $url]: $curlError");
            return ['status' => 0, 'data' => null, 'error' => $curlError];
        }

        $decoded = json_decode($responseBody, true);
        return [
            'status' => $httpStatus,
            'data'   => $decoded['data'] ?? $decoded ?? null,
        ];
    }
}
