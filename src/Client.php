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
        if (empty($params['target_url'])) {
            throw new \InvalidArgumentException('target_url is required');
        }

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
                'User-Agent: ParseEverything-PhpSDK/1.0'
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
            throw new \RuntimeException("API Error ({$httpCode}): " . ($json['detail'] ?? $response));
        }

        return $json ?: [];
    }

    public function batch(array $targetUrls, array $options = []): array {
        $url = $this->baseUrl . '/api/v1/batch';
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
                'User-Agent: ParseEverything-PhpSDK/1.0'
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
