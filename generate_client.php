<?php
$cmd = 'php artisan passport:client --client --name="Final Client"';
$output = shell_exec($cmd);

// Output has ANSI codes, strip them
$clean = preg_replace('/\x1b[^m]*m/', '', $output);

preg_match('/Client ID \.* (.*)/', $clean, $idMatches);
preg_match('/Client Secret \.* (.*)/', $clean, $secretMatches);

$id = trim($idMatches[1] ?? '');
$secret = trim($secretMatches[1] ?? '');

echo "ID: $id\n";
echo "SECRET: $secret\n";

file_put_contents('final_creds.json', json_encode(['id' => $id, 'secret' => $secret]));
