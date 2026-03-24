<?php
namespace R301\API_client;

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
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json'
        ];

        if ($token !== null) {
            $headers[] = 'Authorization: Bearer ' . $token;
        }

        $options = [
            'http' => [
                'method'        => $method,
                'header'        => implode("\r\n", $headers),
                'ignore_errors' => true,
                'timeout'       => 10
            ]
        ];

        if ($body !== null) {
            $options['http']['content'] = json_encode($body);
        }

        $context = stream_context_create($options);
        $responseBody = file_get_contents($url, false, $context);

        $httpStatus = 0;
        if (isset($http_response_header[0]) && preg_match('#HTTP/\S+\s+(\d{3})#', $http_response_header[0], $matches)) {
            $httpStatus = (int)$matches[1];
        }

        if ($responseBody === false) {
            return ['status' => $httpStatus, 'data' => null, 'error' => 'HTTP request failed'];
        }

        $decoded = json_decode($responseBody, true);

        return [
            'status' => $httpStatus,
            'data'   => $decoded['data'] ?? $decoded ?? null,
        ];
    }
}
