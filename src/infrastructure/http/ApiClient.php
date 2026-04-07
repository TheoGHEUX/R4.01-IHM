<?php

declare(strict_types=1);

class ApiClient
{
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

    public function post(string $url, array $payload, int $timeout): array
    {
        $res = $this->send('POST', $url, $payload, $timeout);
        return $this->formatJsonApiResponse($res);
    }

    public function put(string $url, array $payload, int $timeout): array
    {
        $res = $this->send('PUT', $url, $payload, $timeout);
        return $this->formatJsonApiResponse($res);
    }

    public function delete(string $url, int $timeout): array
    {
        $res = $this->send('DELETE', $url, null, $timeout);
        return $this->formatJsonApiResponse($res);
    }

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

