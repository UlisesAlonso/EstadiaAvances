<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function index()
    {
        return view('admin.backup');
    }

    public function restore(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:zip'
        ]);

        try {
            // Guardar el archivo subido temporalmente
            $uploadedFile = $request->file('backup_file');
            $tmpZipPath = $uploadedFile->storeAs('tmp', 'backup_restore_' . time() . '.zip');

            // Extraer archivo .sql del ZIP
            $zip = new \ZipArchive;
            $tmpSqlPath = storage_path('app/tmp_restore.sql');

            if ($zip->open(storage_path("app/" . $tmpZipPath)) === TRUE) {
                // Buscar el archivo .sql dentro del ZIP
                $sqlFound = false;
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $filename = $zip->getNameIndex($i);
                    if (pathinfo($filename, PATHINFO_EXTENSION) === 'sql') {
                        // Extraer el archivo SQL
                        file_put_contents($tmpSqlPath, $zip->getFromIndex($i));
                        $sqlFound = true;
                        break;
                    }
                }
                $zip->close();

                if (!$sqlFound) {
                    Storage::delete($tmpZipPath);
                    return back()->with('error', 'No se encontró un archivo SQL en el ZIP.');
                }
            } else {
                Storage::delete($tmpZipPath);
                return back()->with('error', 'No se pudo abrir el archivo ZIP.');
            }

            // Ejecutar restauración SQL
            $sql = file_get_contents($tmpSqlPath);
            DB::unprepared($sql);

            // Limpiar archivos temporales
            if (file_exists($tmpSqlPath)) {
                unlink($tmpSqlPath);
            }
            Storage::delete($tmpZipPath);

            return back()->with('success', 'Base de datos restaurada correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al restaurar: ' . $e->getMessage());
        }
    }

    public function respaldo()
    {
        try {
            // Obtener configuración de la base de datos
            $connection = config('database.connections.mysql');
            $host = $connection['host'];
            $port = $connection['port'] ?? '3306';
            $username = $connection['username'];
            $password = $connection['password'];
            $database = $connection['database'];
            
            // Crear nombres de archivos
            $timestamp = date('Y-m-d_H-i-s');
            $sqlFilename = "SCV_{$timestamp}.sql";
            $zipFilename = "SCV_{$timestamp}.zip";
            
            // Rutas temporales
            $tmpDir = storage_path('app/tmp');
            if (!file_exists($tmpDir)) {
                mkdir($tmpDir, 0755, true);
            }
            
            $sqlPath = $tmpDir . DIRECTORY_SEPARATOR . $sqlFilename;
            $zipPath = $tmpDir . DIRECTORY_SEPARATOR . $zipFilename;
            
            // Detectar si estamos en Windows
            $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
            
            // Construir comando mysqldump
            // En Windows, intentar encontrar mysqldump en ubicaciones comunes
            $mysqldump = 'mysqldump';
            if ($isWindows) {
                // Rutas comunes de MySQL en Windows
                $commonPaths = [
                    'C:\\xampp\\mysql\\bin\\mysqldump.exe',
                    'C:\\wamp64\\bin\\mysql\\mysql8.0.xx\\bin\\mysqldump.exe',
                    'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
                    'C:\\Program Files\\MySQL\\MySQL Server 5.7\\bin\\mysqldump.exe',
                ];
                
                foreach ($commonPaths as $path) {
                    if (file_exists($path)) {
                        $mysqldump = $path;
                        break;
                    }
                }
            }
            
            // Generar dump de la base de datos
            // Usar --single-transaction para evitar bloqueos y --routines para incluir procedimientos
            $command = sprintf(
                '%s -h %s -P %s -u %s --password=%s --single-transaction --routines --triggers %s > %s 2>&1',
                escapeshellarg($mysqldump),
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($database),
                escapeshellarg($sqlPath)
            );
            
            exec($command, $output, $returnVar);
            
            // Verificar si el archivo se creó y tiene contenido
            if (!file_exists($sqlPath) || filesize($sqlPath) === 0) {
                $errorMsg = !empty($output) ? implode("\n", $output) : 'Error desconocido';
                throw new \Exception('Error al generar el dump de la base de datos: ' . $errorMsg);
            }
            
            // Crear archivo ZIP
            $zip = new \ZipArchive;
            if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
                throw new \Exception('No se pudo crear el archivo ZIP.');
            }
            
            $zip->addFile($sqlPath, $sqlFilename);
            $zip->close();
            
            // Verificar que el ZIP se creó correctamente
            if (!file_exists($zipPath) || filesize($zipPath) === 0) {
                throw new \Exception('El archivo ZIP se creó pero está vacío.');
            }
            
            // Limpiar archivo SQL temporal
            if (file_exists($sqlPath)) {
                unlink($sqlPath);
            }
            
            // Descargar el archivo ZIP
            return response()->download($zipPath, $zipFilename)->deleteFileAfterSend(true);
            
        } catch (\Exception $e) {
            // Limpiar archivos temporales en caso de error
            if (isset($sqlPath) && file_exists($sqlPath)) {
                @unlink($sqlPath);
            }
            if (isset($zipPath) && file_exists($zipPath)) {
                @unlink($zipPath);
            }
            
            return back()->with('error', 'Error al generar el respaldo: ' . $e->getMessage());
        }
    }
}
