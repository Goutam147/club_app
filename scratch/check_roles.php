<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$roles = \Spatie\Permission\Models\Role::with('permissions')->get();
foreach ($roles as $r) {
    echo $r->name . ': ' . $r->permissions->pluck('name')->join(', ') . PHP_EOL;
}

// Check pending transaction count
$pendingCount = \App\Models\Transaction::where('status', 'pending')->count();
echo "\nPending transactions: " . $pendingCount . PHP_EOL;
