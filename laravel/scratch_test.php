<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = \App\Models\User::where('role', 'admin')->first();
$response = $app->handle(Illuminate\Http\Request::create('/dashboard/vouchers', 'GET', [], [], [], [
    'HTTP_ACCEPT' => 'text/html',
]));
echo "Status without auth: " . $response->getStatusCode() . "\n";
echo "Location: " . $response->headers->get('Location') . "\n";
