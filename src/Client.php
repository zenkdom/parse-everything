<?php

namespace ParseEverything;

class Client {
    private string $apiKey;
    private string $baseUrl;

    public function __construct(string $apiKey, string $baseUrl = 'https://parse-everything.com') {
        if (empty($apiKey)) {
            throw new \InvalidArgumentException('API key is required. Get 1,000 free credits at https://parse-everything.com');
        }
        $this->apiKey = trim($apiKey);
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public function scrape(array $params): array {
        $targetUrl = $params['target_url'] ?? $params['url'] ?? null;
        if (empty($targetUrl)) {
            throw new \InvalidArgumentException('target_url (or url) is required');
        }

        $defaultParams = [
            'country_code' => 'US',
            'render_js' => true,
            'auto_detect' => true,
            'method' => 'GET'
        ];
        $params = array_merge($defaultParams, $params);
        $params['target_url'] = $targetUrl;
        $params['method'] = strtoupper($params['method'] ?? 'GET');

        $url = $this->baseUrl . '/api/v1/extract';
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => ($params['timeout'] ?? 40) + 10,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey,
                'User-Agent: ParseEverything-PhpSDK/1.1.4'
            ]
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new \RuntimeException("cURL Error: " . $error);
        }

        $json = json_decode($response, true);
        if ($httpCode >= 400) {
            throw new \RuntimeException("API Error ({$httpCode}): " . ($json['message'] ?? $json['detail'] ?? $response));
        }

        return $json ?: [];
    }

    public function get(string $url, array $options = []): array {
        return $this->scrape(array_merge(['target_url' => $url, 'method' => 'GET'], $options));
    }

    public function post(string $url, $body = null, array $options = []): array {
        $params = array_merge(['target_url' => $url, 'method' => 'POST'], $options);
        if ($body !== null) {
            $params['body'] = $body;
        }
        return $this->scrape($params);
    }

    public function batch(array $targetUrls, array $options = []): array {
        $url = $this->baseUrl . '/api/v1/batch';
        $defaultOptions = [
            'country_code' => 'US',
            'render_js' => true
        ];
        $options = array_merge($defaultOptions, $options);
        $payload = array_merge(['target_urls' => $targetUrls], $options);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey,
                'User-Agent: ParseEverything-PhpSDK/1.1.4'
            ]
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $json = json_decode($response, true);
        if ($httpCode >= 400) {
            throw new \RuntimeException("Batch Error ({$httpCode}): " . ($json['detail'] ?? $response));
        }
        return $json ?: [];
    }
}
