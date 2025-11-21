<?php

/**
 * Script para exportar la base de datos
 * Uso: php database/export_db.php
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$dbName = config('database.connections.mysql.database');
$dbUser = config('database.connections.mysql.username');
$dbPass = config('database.connections.mysql.password');
$dbHost = config('database.connections.mysql.host');
$dbPort = config('database.connections.mysql.port', 3306);

$fileName = 'database_' . date('Y-m-d_His') . '.sql';
$filePath = __DIR__ . '/dumps/' . $fileName;

// Crear directorio si no existe
$dir = __DIR__ . '/dumps';
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

// Intentar diferentes ubicaciones de mysqldump
$mysqldumpPaths = [
    'mysqldump',
    'C:\\xampp\\mysql\\bin\\mysqldump.exe',
    'C:\\wamp64\\bin\\mysql\\mysql8.0.xx\\bin\\mysqldump.exe',
    'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
    'C:\\Program Files\\xampp\\mysql\\bin\\mysqldump.exe',
];

$mysqldump = null;
foreach ($mysqldumpPaths as $path) {
    if (is_executable($path) || shell_exec("where $path 2>nul")) {
        $mysqldump = $path;
        break;
    }
}

if (!$mysqldump) {
    // Intentar encontrar mysqldump en el PATH
    $output = shell_exec('where mysqldump 2>nul');
    if ($output) {
        $mysqldump = trim(explode("\n", $output)[0]);
    }
}

if (!$mysqldump) {
    echo "ERROR: No se encontró mysqldump. Por favor, exporta la base de datos manualmente.\n";
    echo "Base de datos: {$dbName}\n";
    echo "Host: {$dbHost}\n";
    echo "Usuario: {$dbUser}\n";
    exit(1);
}

// Construir comando
$command = sprintf(
    '"%s" -h%s -P%s -u%s -p%s %s > "%s"',
    $mysqldump,
    escapeshellarg($dbHost),
    escapeshellarg($dbPort),
    escapeshellarg($dbUser),
    escapeshellarg($dbPass),
    escapeshellarg($dbName),
    escapeshellarg($filePath)
);

echo "Exportando base de datos '{$dbName}'...\n";
echo "Comando: {$command}\n\n";

exec($command . ' 2>&1', $output, $returnVar);

if ($returnVar === 0 && file_exists($filePath) && filesize($filePath) > 0) {
    echo "✓ Base de datos exportada exitosamente!\n";
    echo "Archivo: {$filePath}\n";
    echo "Tamaño: " . number_format(filesize($filePath) / 1024, 2) . " KB\n";
} else {
    echo "ERROR al exportar la base de datos.\n";
    if (!empty($output)) {
        echo "Salida: " . implode("\n", $output) . "\n";
    }
    exit(1);
}

