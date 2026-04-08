<?php

declare(strict_types=1);

/**
 * ApiCLient qui appelle des APIs JSON via cURL.
 *
 */
class ApiClient
{
    /**
     * Effectue un GET et exige un JSON valide (sinon retourne ok=false).
     *
     * @param string $url URL complète de la ressource.
     * @param int $timeout Timeout (secondes) pour la requête et la connexion.
     * @return array{ok:bool, error:?string, http_code:int, data:mixed, raw:?string}
     */
    public function get(string $url, int $timeout): array
    {
        $res = $this->send('GET', $url, null, $timeout);
        if (!$res['ok']) {
            return ['ok' => false, 'error' => $res['error'], 'http_code' => $res['http_code'], 'data' => null, 'raw' => $res['body'] ?? null];
        }

        $decoded = $this->decodeJson($res['body']);
        if (!$decoded['ok']) {
            return ['ok' => false, 'error' => 'JSON invalide', 'http_code' => $res['http_code'], 'data' => null, 'raw' => $res['body']];
        }

        return ['ok' => true, 'error' => null, 'http_code' => $res['http_code'], 'data' => $decoded['data'], 'raw' => $res['body']];
    }

    /**
     * Effectue un POST JSON.
     *
     * @param string $url URL complète de la ressource.
     * @param array $payload Payload JSON (sera encodé).
     * @param int $timeout Timeout (secondes).
     * @return array{ok:bool, error:?string, http_code:int, data:mixed, raw:?string}
     */
    public function post(string $url, array $payload, int $timeout): array
    {
        $res = $this->send('POST', $url, $payload, $timeout);
        return $this->formatJsonApiResponse($res);
    }

    /**
     * Effectue un PUT JSON.
     *
     * @param string $url URL complète de la ressource.
     * @param array $payload Payload JSON (sera encodé).
     * @param int $timeout Timeout (secondes).
     * @return array{ok:bool, error:?string, http_code:int, data:mixed, raw:?string}
     */
    public function put(string $url, array $payload, int $timeout): array
    {
        $res = $this->send('PUT', $url, $payload, $timeout);
        return $this->formatJsonApiResponse($res);
    }

    /**
     * Effectue un DELETE.
     *
     * @param string $url URL complète de la ressource.
     * @param int $timeout Timeout (secondes).
     * @return array{ok:bool, error:?string, http_code:int, data:mixed, raw:?string}
     */
    public function delete(string $url, int $timeout): array
    {
        $res = $this->send('DELETE', $url, null, $timeout);
        return $this->formatJsonApiResponse($res);
    }

    /**
     * Formate une réponse réseau (send) vers la convention "JSON API" du projet.
     *
     * - Si erreur réseau : ok=false
     * - Sinon ok=true, et data sera présent si le corps est un JSON valide (sinon null)
     *
     * @param array{ok:bool, http_code:int, body:string, error:string} $res
     * @return array{ok:bool, error:?string, http_code:int, data:mixed, raw:?string}
     */
    private function formatJsonApiResponse(array $res): array
    {
        if (!$res['ok']) {
            return ['ok' => false, 'error' => $res['error'], 'http_code' => $res['http_code'], 'data' => null, 'raw' => $res['body'] ?? null];
        }

        $data = null;
        $decoded = $this->decodeJson($res['body']);
        if ($decoded['ok']) {
            $data = $decoded['data'];
        }

        return ['ok' => true, 'error' => null, 'http_code' => $res['http_code'], 'data' => $data, 'raw' => $res['body']];
    }

    /**
     * Décode un corps HTTP en JSON.
     *
     * @param string $body Corps brut.
     * @return array{ok:bool, data:mixed}
     */
    private function decodeJson(string $body): array
    {
        if (trim($body) === '') {
            return ['ok' => false, 'data' => null];
        }

        $decoded = json_decode($body, true);
        if (!is_array($decoded)) {
            return ['ok' => false, 'data' => null];
        }

        return ['ok' => true, 'data' => $decoded];
    }

    /**
     * Envoie une requête HTTP via cURL.
     *
     * @param string $method Méthode HTTP (GET/POST/PUT/DELETE).
     * @param string $url URL complète.
     * @param array|null $payload Payload à encoder en JSON (null si pas de body).
     * @param int $timeout Timeout (secondes).
     *
     * @return array{ok:bool, http_code:int, body:string, error:string}
     */
    private function send(string $method, string $url, ?array $payload, int $timeout): array
    {
        $ch = curl_init($url);
        $headers = ['Accept: application/json'];

        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_CONNECTTIMEOUT => $timeout,
            CURLOPT_CUSTOMREQUEST => $method,
        ];

        if ($payload !== null) {
            $json = json_encode($payload, JSON_UNESCAPED_UNICODE);
            $options[CURLOPT_POSTFIELDS] = $json;
            $headers[] = 'Content-Type: application/json; charset=utf-8';
        }

        $options[CURLOPT_HTTPHEADER] = $headers;
        curl_setopt_array($ch, $options);

        $body = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'ok' => ($body !== false),
            'http_code' => $httpCode,
            'body' => $body === false ? '' : $body,
            'error' => $error,
        ];
    }
}