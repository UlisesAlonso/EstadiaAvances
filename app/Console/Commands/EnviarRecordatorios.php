<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Paciente;
use Illuminate\Support\Facades\Mail;
use App\Mail\RecordatorioCita;
use Carbon\Carbon;

class EnviarRecordatorios extends Command
{
    protected $signature = 'recordatorios:enviar {--test : Modo de prueba, no envía correos reales}';
    protected $description = 'Envía recordatorios de cita a todos los pacientes con cita programada para mañana';

    public function handle()
    {
        $modoPrueba = $this->option('test');
        
        // Buscar citas solo para mañana
        $mañana = Carbon::now()->addDay()->startOfDay();
        $finDia = Carbon::now()->addDay()->endOfDay();

        if ($modoPrueba) {
            $this->warn("⚠️  MODO DE PRUEBA ACTIVADO - No se enviarán correos reales");
        }

        $this->info("Buscando citas para mañana: {$mañana->format('Y-m-d H:i:s')} hasta {$finDia->format('Y-m-d H:i:s')}");

        // Obtener todas las citas de mañana con relación al paciente
        $citas = \App\Models\Cita::with(['paciente.usuario', 'medico.usuario'])
            ->whereBetween('fecha', [$mañana, $finDia])
            ->get();

        $this->info("Citas encontradas: " . $citas->count());

        if ($citas->isEmpty()) {
            $this->warn("No se encontraron citas para mañana.");
            $this->info("Verificando todas las citas en la base de datos...");
            $totalCitas = \App\Models\Cita::count();
            $this->info("Total de citas en la base de datos: {$totalCitas}");
            
            if ($totalCitas > 0) {
                $todasCitas = \App\Models\Cita::with(['paciente.usuario', 'medico.usuario'])
                    ->orderBy('fecha', 'asc')
                    ->get();
                
                $this->info("");
                $this->info("Todas las citas en la base de datos:");
                foreach ($todasCitas as $cita) {
                    $fechaFormateada = Carbon::parse($cita->fecha)->format('Y-m-d H:i');
                    $pacienteNombre = $cita->paciente && $cita->paciente->usuario 
                        ? $cita->paciente->usuario->nombre . ' ' . $cita->paciente->usuario->apPaterno 
                        : 'N/A';
                    $correo = $cita->paciente && $cita->paciente->usuario && $cita->paciente->usuario->correo
                        ? $cita->paciente->usuario->correo
                        : 'Sin correo';
                    $this->line("  - ID: {$cita->id_cita}, Fecha: {$fechaFormateada}, Paciente: {$pacienteNombre}, Correo: {$correo}");
                }
            } else {
                $this->warn("No hay citas en la base de datos. Crea algunas citas primero.");
            }
            return;
        }

        $enviados = 0;
        $errores = 0;

        foreach ($citas as $cita) {
            try {
                $this->line("Procesando cita ID: {$cita->id_cita}");
                
                if (!$cita->paciente) {
                    $this->warn("  ⚠ Cita ID {$cita->id_cita} no tiene paciente asociado");
                    $errores++;
                    continue;
                }

                if (!$cita->paciente->usuario) {
                    $this->warn("  ⚠ Cita ID {$cita->id_cita} - Paciente ID {$cita->paciente->id_paciente} no tiene usuario asociado");
                    $errores++;
                    continue;
                }

                if (!$cita->paciente->usuario->correo) {
                    $this->warn("  ⚠ Cita ID {$cita->id_cita} - Usuario no tiene correo: " . $cita->paciente->usuario->nombre);
                    $errores++;
                    continue;
                }

                $correo = $cita->paciente->usuario->correo;
                
                if ($modoPrueba) {
                    $this->info("  📧 [PRUEBA] Se enviaría recordatorio a: {$correo}");
                    $this->line("     - Fecha cita: {$cita->fecha}");
                    $this->line("     - Paciente: {$cita->paciente->usuario->nombre} {$cita->paciente->usuario->apPaterno}");
                    $enviados++;
                } else {
                    $this->info("  📧 Enviando recordatorio a: {$correo}");
                    
                    try {
                        Mail::to($correo)->send(new RecordatorioCita($cita));
                        $enviados++;
                        $this->info("  ✅ Recordatorio enviado exitosamente a: {$correo}");
                    } catch (\Exception $mailException) {
                        $this->error("  ❌ Error al enviar correo: " . $mailException->getMessage());
                        $errores++;
                    }
                }
                
            } catch (\Exception $e) {
                $this->error("  ❌ Error al enviar recordatorio para cita ID {$cita->id_cita}: " . $e->getMessage());
                $this->error("  Stack trace: " . $e->getTraceAsString());
                $errores++;
            }
        }

        $this->info("");
        $this->info("═══════════════════════════════════════");
        $this->info("Proceso completado. Enviados: {$enviados}, Errores: {$errores}");
        $this->info("═══════════════════════════════════════");
    }
}
