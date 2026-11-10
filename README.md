# parse-everything/client

Official PHP Client Library for [Parse Everything](https://parse-everything.com) — Anti-Bot Bypass & Web Extraction API.

> 🛡️ **Zero Risk Policy:** Failed, blocked, or unreachable requests cost **0 credits**. You only pay for successful extractions (HTTP 200–399).

---

## 📦 Installation

```bash
composer require parse-everything/client
```

---

## ⚡ Usage

### 1. Standard GET Extraction
```php
use ParseEverything\Client;

$client = new Client('pe_live_YOUR_API_KEY');

$result = $client->scrape([
    'target_url' => 'https://parse-everything.com/demo/cloudflare',
    'country_code' => 'US',
    'render_js' => true
]);

echo "HTTP Status: " . $result['data']['http_status'] . "\n";
echo "Page Title: " . $result['data']['page_title'] . "\n";
```

### 2. POST / PUT Request Forwarding (APIs & Search Forms)
```php
$postResult = $client->post('https://example.com/api/search', [
    'category' => 'laptops',
    'in_stock' => true
]);

echo "Status: " . $postResult['data']['http_status'] . "\n";
```
