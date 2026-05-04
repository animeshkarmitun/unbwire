<?php

/**
 * Database Restore Script for Laravel
 * 
 * This script restores a SQL dump to the database using configuration from .env.
 * Usage: php db_restore.php <path_to_sql_file>
 * 
 * World-class features:
 * - Bootstraps Laravel to use existing .env configuration.
 * - Safety confirmation prompt to prevent accidental data loss.
 * - Validates file existence and readability.
 * - Handles restoration via the standard mysql CLI.
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\Config;

if ($argc < 2) {
    echo "Usage: php db_restore.php <path_to_sql_file>\n";
    echo "Example: php db_restore.php storage/backups/unb_news_2024-05-03.sql\n";
    exit(1);
}

$fileToRestore = $argv[1];

if (!file_exists($fileToRestore)) {
    die("ERROR: File not found: {$fileToRestore}\n");
}

if (!is_readable($fileToRestore)) {
    die("ERROR: File is not readable: {$fileToRestore}\n");
}

// Get Database Configuration from Laravel
$connection = Config::get('database.default');
$config = Config::get("database.connections.{$connection}");

if ($config['driver'] !== 'mysql') {
    die("ERROR: This script currently only supports MySQL/MariaDB.\n");
}

$host = $config['host'];
$port = $config['port'];
$database = $config['database'];
$username = $config['username'];
$password = $config['password'];

echo "!!! CAUTION !!!\n";
echo "This will OVERWRITE the current database: '{$database}'\n";
echo "Are you sure you want to proceed? Type 'yes' to confirm: ";

$handle = fopen("php://stdin", "r");
$line = fgets($handle);
if (trim($line) != 'yes') {
    echo "Aborted. No changes were made.\n";
    exit;
}

echo "\nStarting restoration from: {$fileToRestore}...\n";

// Define potential paths for mysql (WAMP support)
$mysqlPath = 'mysql';
$commonPaths = [
    'C:\wamp64\bin\mysql\mysql9.1.0\bin\mysql.exe',
    'C:\xampp\mysql\bin\mysql.exe',
];

foreach ($commonPaths as $path) {
    if (file_exists($path)) {
        $mysqlPath = '"' . $path . '"';
        break;
    }
}

// Construct mysql command
$command = sprintf(
    '%s --user=%s %s --host=%s --port=%s %s < %s',
    $mysqlPath,
    escapeshellarg($username),
    $password ? '--password=' . escapeshellarg($password) : '',
    escapeshellarg($host),
    escapeshellarg($port),
    escapeshellarg($database),
    escapeshellarg($fileToRestore)
);

// Execute the command
$output = [];
$returnVar = -1;
exec($command, $output, $returnVar);

if ($returnVar === 0) {
    echo "SUCCESS: Database restored successfully!\n";
} else {
    echo "ERROR: Restoration failed with exit code {$returnVar}.\n";
    echo "Possible causes:\n";
    echo "1. 'mysql' CLI is not in your system PATH.\n";
    echo "2. The SQL file is corrupted or contains syntax errors.\n";
    echo "3. The database user lacks sufficient permissions to drop/create tables.\n";
}
