<?php

require __DIR__.'/vendor/autoload.php';

$clientId = '019b2990-b4c6-7264-87ea-2642a1c0b8c9';
$clientSecret = 'TeGoLDGmdOdvNOYpY8902hYMZ4pTDtKVQsuSuVIC';
$baseUrl = 'http://127.0.0.1:8000';

// 1. Get Token
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseUrl/oauth/token");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'grant_type' => 'client_credentials',
    'client_id' => $clientId,
    'client_secret' => $clientSecret,
    'scope' => ''
]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$tokenData = json_decode($response, true);
if (!isset($tokenData['access_token'])) {
    die("Failed to get token: " . $response . "\n");
}

$accessToken = $tokenData['access_token'];
echo "Token Acquired.\n";

// 2. Create Product (POST)
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseUrl/api/products");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $accessToken",
    "Accept: application/json"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'name' => 'Test Product',
    'price' => 99.99,
    'stock' => 10
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$createResponse = curl_exec($ch);
curl_close($ch);
echo "Create Response: " . $createResponse . "\n";

// 3. List Products (GET)
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "$baseUrl/api/products");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $accessToken",
    "Accept: application/json"
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$listResponse = curl_exec($ch);
curl_close($ch);
echo "List Response: " . $listResponse . "\n";
