<?php

/**
 * Database Backup Script for Laravel
 * 
 * This script generates a SQL dump of the database using configuration from .env.
 * Usage: php db_backup.php
 * 
 * World-class features:
 * - Bootstraps Laravel to use existing .env configuration.
 * - Automatic directory creation in storage/backups.
 * - Single-transaction dump for InnoDB consistency.
 * - Informative output and error reporting.
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\Config;

// Get Database Configuration from Laravel
$connection = Config::get('database.default');
$config = Config::get("database.connections.{$connection}");

if ($config['driver'] !== 'mysql') {
    die("Error: This script currently only supports MySQL/MariaDB.\n");
}

$host = $config['host'];
$port = $config['port'];
$database = $config['database'];
$username = $config['username'];
$password = $config['password'];

// Define Backup Directory
$backupDir = storage_path('backups');
if (!is_dir($backupDir)) {
    if (!mkdir($backupDir, 0755, true)) {
        die("Error: Failed to create backup directory: {$backupDir}\n");
    }
}

// Generate Filename with Timestamp
$filename = $database . '_' . date('Y-m-d_H-i-s') . '.sql';
$filePath = $backupDir . DIRECTORY_SEPARATOR . $filename;

echo "--- UNB News Database Backup ---\n";
echo "Database: {$database}\n";
echo "Host:     {$host}:{$port}\n";
echo "File:     {$filePath}\n";
echo "--------------------------------\n";
echo "Starting backup...\n";

// Define potential paths for mysqldump (WAMP support)
$mysqldumpPath = 'mysqldump';
$commonPaths = [
    'C:\wamp64\bin\mysql\mysql9.1.0\bin\mysqldump.exe',
    'C:\xampp\mysql\bin\mysqldump.exe',
];

foreach ($commonPaths as $path) {
    if (file_exists($path)) {
        $mysqldumpPath = '"' . $path . '"';
        break;
    }
}

// Construct mysqldump command
// --single-transaction is essential for production databases to avoid locking tables
$command = sprintf(
    '%s --user=%s %s --host=%s --port=%s --single-transaction %s > %s',
    $mysqldumpPath,
    escapeshellarg($username),
    $password ? '--password=' . escapeshellarg($password) : '',
    escapeshellarg($host),
    escapeshellarg($port),
    escapeshellarg($database),
    escapeshellarg($filePath)
);

// Execute the command
$output = [];
$returnVar = -1;
exec($command, $output, $returnVar);

if ($returnVar === 0) {
    echo "SUCCESS: Backup completed successfully.\n";
    echo "Location: {$filePath}\n";
    echo "Size:     " . round(filesize($filePath) / 1024 / 1024, 2) . " MB\n";
} else {
    echo "ERROR: Backup failed with exit code {$returnVar}.\n";
    echo "Possible causes:\n";
    echo "1. 'mysqldump' is not in your system PATH.\n";
    echo "2. Database credentials in .env are incorrect.\n";
    echo "3. Insufficient permissions to write to {$backupDir}.\n";
}
