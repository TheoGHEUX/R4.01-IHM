<?php

require_once __DIR__ . '/../http.php';

function api_get_json(string $url, int $timeout): array {
    $res = http_get($url, $timeout);
    if (!$res['ok']) {
        return ['ok' => false, 'error' => $res['error'], 'http_code' => $res['http_code'], 'data' => null];
    }
    $data = json_decode($res['body'], true);
    if (!is_array($data)) {
        return ['ok' => false, 'error' => 'JSON invalide', 'http_code' => $res['http_code'], 'data' => null, 'raw' => $res['body']];
    }
    return ['ok' => true, 'error' => null, 'http_code' => $res['http_code'], 'data' => $data];
}