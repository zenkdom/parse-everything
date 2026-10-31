# parse-everything/client

Official PHP Client Library for [Parse Everything](https://parse-everything.com) — Anti-Bot Bypass & Web Extraction API.

---

## 📦 Installation

```bash
composer require parse-everything/client
```

---

## ⚡ Usage

```php
use ParseEverything\Client;

$client = new Client('pe_live_YOUR_API_KEY');

$result = $client->scrape([
    'target_url' => 'https://nowsecure.nl',
    'bypass_cloudflare' => true
]);

echo "HTTP Status: " . $result['data']['http_status'] . "\n";
echo "Page Title: " . $result['data']['page_title'] . "\n";
```
