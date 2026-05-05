<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$token = env('META_PAGE_TOKEN');
$senderId = App\Models\Contact::latest()->first()->external_id;

echo "Token exists: " . ($token ? 'Yes' : 'No') . "\n";
echo "Sender ID: " . $senderId . "\n";

$response = Illuminate\Support\Facades\Http::withoutVerifying()->get('https://graph.facebook.com/v19.0/' . $senderId, [
    'fields' => 'name,profile_pic',
    'access_token' => $token
]);

print_r($response->json());
