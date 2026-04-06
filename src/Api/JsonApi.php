<?php

require_once __DIR__ . '/../http.php';

function api_get_json(string $url, int $timeout): array {
    $res = http_get($url, $timeout);
    if (!$res['ok']) {
        return ['ok' => false, 'error' => $res['error'], 'http_code' => $res['http_code'], 'data' => null, 'raw' => $res['body'] ?? null];
    }
    $data = json_decode($res['body'], true);
    if (!is_array($data)) {
        return ['ok' => false, 'error' => 'JSON invalide', 'http_code' => $res['http_code'], 'data' => null, 'raw' => $res['body']];
    }
    return ['ok' => true, 'error' => null, 'http_code' => $res['http_code'], 'data' => $data, 'raw' => $res['body']];
}

function api_post_json(string $url, array $payload, int $timeout): array {
    $res = http_post_json($url, $payload, $timeout);
    if (!$res['ok']) {
        return ['ok' => false, 'error' => $res['error'], 'http_code' => $res['http_code'], 'data' => null, 'raw' => $res['body'] ?? null];
    }
    $data = null;
    if (is_string($res['body']) && trim($res['body']) !== '') {
        $decoded = json_decode($res['body'], true);
        if (is_array($decoded)) $data = $decoded;
    }
    return ['ok' => true, 'error' => null, 'http_code' => $res['http_code'], 'data' => $data, 'raw' => $res['body']];
}

function api_put_json(string $url, array $payload, int $timeout): array {
    $res = http_put_json($url, $payload, $timeout);
    if (!$res['ok']) {
        return ['ok' => false, 'error' => $res['error'], 'http_code' => $res['http_code'], 'data' => null, 'raw' => $res['body'] ?? null];
    }
    $data = null;
    if (is_string($res['body']) && trim($res['body']) !== '') {
        $decoded = json_decode($res['body'], true);
        if (is_array($decoded)) $data = $decoded;
    }
    return ['ok' => true, 'error' => null, 'http_code' => $res['http_code'], 'data' => $data, 'raw' => $res['body']];
}

function api_delete_json(string $url, int $timeout): array {
    // DELETE often returns empty body; keep uniform structure
    $res = http_delete_json($url, $timeout);
    if (!$res['ok']) {
        return ['ok' => false, 'error' => $res['error'], 'http_code' => $res['http_code'], 'data' => null, 'raw' => $res['body'] ?? null];
    }
    $data = null;
    if (is_string($res['body']) && trim($res['body']) !== '') {
        $decoded = json_decode($res['body'], true);
        if (is_array($decoded)) $data = $decoded;
    }
    return ['ok' => true, 'error' => null, 'http_code' => $res['http_code'], 'data' => $data, 'raw' => $res['body']];
}