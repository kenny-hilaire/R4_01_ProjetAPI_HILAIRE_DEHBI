<?php
namespace R301\API_client;

/**
 * Classe utilitaire pour appeler les APIs backend via HTTP.
 * Toutes les requêtes transmettent automatiquement le JWT.
 */
class ApiClient {

    public static function get(string $endpoint, ?string $token = null, array $queryParams = []): array {
        $url = BACKEND_API_URL . $endpoint;
        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }
        return self::request('GET', $url, null, $token);
    }

    public static function post(string $endpoint, array $body, ?string $token = null): array {
        $url = BACKEND_API_URL . $endpoint;
        return self::request('POST', $url, $body, $token);
    }

    public static function put(string $endpoint, array $body, ?string $token = null): array {
        $url = BACKEND_API_URL . $endpoint;
        return self::request('PUT', $url, $body, $token);
    }

    public static function delete(string $endpoint, ?string $token = null): array {
        $url = BACKEND_API_URL . $endpoint;
        return self::request('DELETE', $url, null, $token);
    }

    public static function login(string $login, string $password): array {
        $url = AUTH_API_URL . '/login';
        return self::request('POST', $url, ['login' => $login, 'password' => $password], null);
    }

    /**
     * Méthode centrale
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

        // On récupére le code HTTP
        $httpStatus = 0;
        if (isset($http_response_header[0]) && preg_match('#HTTP/\S+\s+(\d{3})#', $http_response_header[0], $matches)) {
            $httpStatus = (int)$matches[1];
        }

        // cas d' erreur réseau
        if ($responseBody === false) {
            return [
                'status' => $httpStatus,
                'data'   => null,
                'message'=> 'HTTP request failed'
            ];
        }

        $decoded = json_decode($responseBody, true);

        // cas ou le JSON invalide
        if ($decoded === null) {
            return [
                'status' => $httpStatus,
                'data'   => null,
                'message'=> 'Invalid JSON response'
            ];
        }

        
        return [
            'status'  => $httpStatus,
            'data'    => $decoded['data'] ?? null,              
            'message' => $decoded['status_message'] ?? null
        ];
    }
}