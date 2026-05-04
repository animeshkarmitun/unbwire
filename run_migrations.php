<?php

/**
 * Migration Runner Script for Laravel
 * 
 * This script runs pending database migrations.
 * Useful for environments where SSH access is limited.
 * Usage: php run_migrations.php
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\Artisan;

echo "--- UNB News Migration Runner ---\n";
echo "Environment: " . app()->environment() . "\n";
echo "Starting migrations...\n";

try {
    // --force is required to run migrations in production environment
    $exitCode = Artisan::call('migrate', [
        '--force' => true,
    ]);

    echo "\nOutput:\n";
    echo Artisan::output();

    if ($exitCode === 0) {
        echo "\nSUCCESS: Migrations completed successfully.\n";
    } else {
        echo "\nERROR: Migrations failed with exit code {$exitCode}.\n";
    }
} catch (\Exception $e) {
    echo "\nFATAL ERROR: " . $e->getMessage() . "\n";
}
