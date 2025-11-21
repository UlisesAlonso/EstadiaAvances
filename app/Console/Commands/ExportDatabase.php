<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class ExportDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:export {--file=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Exporta la base de datos a un archivo SQL';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dbName = Config::get('database.connections.mysql.database');
        $dbUser = Config::get('database.connections.mysql.username');
        $dbPass = Config::get('database.connections.mysql.password');
        $dbHost = Config::get('database.connections.mysql.host');
        $dbPort = Config::get('database.connections.mysql.port', 3306);

        $fileName = $this->option('file') ?: 'database_' . date('Y-m-d_His') . '.sql';
        $filePath = database_path('dumps/' . $fileName);

        // Crear directorio si no existe
        $dir = database_path('dumps');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Construir comando mysqldump
        $command = sprintf(
            'mysqldump -h%s -P%s -u%s -p%s %s > %s',
            escapeshellarg($dbHost),
            escapeshellarg($dbPort),
            escapeshellarg($dbUser),
            escapeshellarg($dbPass),
            escapeshellarg($dbName),
            escapeshellarg($filePath)
        );

        $this->info("Exportando base de datos '{$dbName}'...");
        
        // Ejecutar comando
        exec($command . ' 2>&1', $output, $returnVar);

        if ($returnVar === 0) {
            $this->info("Base de datos exportada exitosamente a: {$filePath}");
            return 0;
        } else {
            $this->error("Error al exportar la base de datos:");
            $this->error(implode("\n", $output));
            return 1;
        }
    }
}
