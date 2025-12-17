<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$client = \Laravel\Passport\Client::where('name', 'M2M Client')->first();
echo "CLIENT_ID:" . $client->id . "\n";
echo "CLIENT_SECRET:" . $client->secret . "\n";
