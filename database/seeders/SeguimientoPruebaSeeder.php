<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Paciente;
use App\Models\Medico;
use App\Models\ObservacionSeguimiento;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class SeguimientoPruebaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creando observaciones de seguimiento de prueba...');

        // Obtener médicos y pacientes existentes
        $medicos = Medico::with('usuario')->get();
        $pacientes = Paciente::with('usuario')->get();

        if ($medicos->isEmpty()) {
            $this->command->warn('No hay médicos en la base de datos. Creando médicos de prueba...');
            $medicos = $this->crearMedicos();
        }

        if ($pacientes->isEmpty()) {
            $this->command->warn('No hay pacientes en la base de datos. Creando pacientes de prueba...');
            $pacientes = $this->crearPacientes();
        }

        // Tipos de observación
        $tiposObservacion = [
            'general',
            'evolucion',
            'alerta',
            'seguimiento',
            'control',
            'revision',
            'mejoria',
            'empeoramiento',
            'estable',
            'recuperacion',
        ];

        // Observaciones de ejemplo con variedad
        $observacionesData = [
            // Observaciones generales
            ['tipo' => 'general', 'observacion' => 'Paciente presenta buen estado general. Signos vitales estables.'],
            ['tipo' => 'general', 'observacion' => 'Control de rutina. Sin complicaciones.'],
            ['tipo' => 'general', 'observacion' => 'Revisión médica de seguimiento.'],
            
            // Observaciones de evolución
            ['tipo' => 'evolucion', 'observacion' => 'Evolución favorable. Mejora en síntomas reportados.'],
            ['tipo' => 'evolucion', 'observacion' => 'Progreso positivo en el tratamiento.'],
            ['tipo' => 'evolucion', 'observacion' => 'Evolución satisfactoria. Continuar con tratamiento actual.'],
            ['tipo' => 'evolucion', 'observacion' => 'Mejora significativa en parámetros clínicos.'],
            
            // Alertas
            ['tipo' => 'alerta', 'observacion' => 'ALERTA: Requiere atención inmediata. Signos de complicación.'],
            ['tipo' => 'alerta', 'observacion' => 'Atención urgente: Cambios en signos vitales.'],
            ['tipo' => 'alerta', 'observacion' => 'Vigilar de cerca. Posible reacción adversa.'],
            
            // Seguimiento
            ['tipo' => 'seguimiento', 'observacion' => 'Seguimiento continuo del caso.'],
            ['tipo' => 'seguimiento', 'observacion' => 'Control de seguimiento programado.'],
            ['tipo' => 'seguimiento', 'observacion' => 'Monitoreo activo del paciente.'],
            
            // Control
            ['tipo' => 'control', 'observacion' => 'Control de medicación. Verificar adherencia al tratamiento.'],
            ['tipo' => 'control', 'observacion' => 'Revisión de parámetros de control.'],
            ['tipo' => 'control', 'observacion' => 'Control de peso y presión arterial.'],
            
            // Revisión
            ['tipo' => 'revision', 'observacion' => 'Revisión de resultados de análisis.'],
            ['tipo' => 'revision', 'observacion' => 'Revisión de historial clínico.'],
            
            // Mejora
            ['tipo' => 'mejoria', 'observacion' => 'Paciente muestra mejoría notable.'],
            ['tipo' => 'mejoria', 'observacion' => 'Recuperación progresiva.'],
            
            // Empeoramiento
            ['tipo' => 'empeoramiento', 'observacion' => 'Empeoramiento de síntomas. Ajustar tratamiento.'],
            ['tipo' => 'empeoramiento', 'observacion' => 'Deterioro en condición. Revisar estrategia.'],
            
            // Estable
            ['tipo' => 'estable', 'observacion' => 'Condición estable. Mantener tratamiento actual.'],
            ['tipo' => 'estable', 'observacion' => 'Estado clínico estable. Sin cambios significativos.'],
            
            // Recuperación
            ['tipo' => 'recuperacion', 'observacion' => 'Paciente en proceso de recuperación.'],
            ['tipo' => 'recuperacion', 'observacion' => 'Recuperación completa. Alta médica programada.'],
        ];

        $this->command->info('Generando 80 observaciones de seguimiento de prueba...');
        $contador = 0;

        for ($i = 0; $i < 80; $i++) {
            $observacionData = $observacionesData[array_rand($observacionesData)];
            $medico = $medicos->random();
            $paciente = $pacientes->random();
            
            // Fechas distribuidas en los últimos 6 meses y próximos
            $fechaObservacion = Carbon::now()->subDays(rand(0, 180))->addHours(rand(9, 17));
            if (rand(0, 1)) { // 50% de probabilidad de que sea futuro
                $fechaObservacion = Carbon::now()->addDays(rand(1, 90))->addHours(rand(9, 17));
            }

            ObservacionSeguimiento::create([
                'id_paciente' => $paciente->id_paciente,
                'id_medico' => $medico->id_medico,
                'observacion' => $observacionData['observacion'],
                'fecha_observacion' => $fechaObservacion,
                'tipo' => $observacionData['tipo'],
                'fecha_creacion' => Carbon::now(),
            ]);

            $contador++;
        }

        $this->command->info("✓ Se crearon {$contador} observaciones de seguimiento de prueba exitosamente!");

        // Mostrar estadísticas
        $this->command->info('Distribución de observaciones:');
        $total = ObservacionSeguimiento::count();
        $this->command->info("  - Total: {$total} observaciones");

        $this->command->info('Distribución por tipo:');
        ObservacionSeguimiento::select('tipo', \DB::raw('count(*) as total'))
            ->groupBy('tipo')
            ->get()
            ->each(function ($item) {
                $tipo = $item->tipo ?: 'Sin tipo';
                $this->command->info("  - {$tipo}: {$item->total} observaciones");
            });

        $this->command->info('Distribución por médico:');
        ObservacionSeguimiento::select('id_medico', \DB::raw('count(*) as total'))
            ->groupBy('id_medico')
            ->with('medico.usuario')
            ->get()
            ->each(function ($item) {
                $nombre = $item->medico->usuario->nombre . ' ' . $item->medico->usuario->apPaterno;
                $this->command->info("  - {$nombre}: {$item->total} observaciones");
            });

        $this->command->info('Distribución por paciente:');
        ObservacionSeguimiento::select('id_paciente', \DB::raw('count(*) as total'))
            ->groupBy('id_paciente')
            ->with('paciente.usuario')
            ->get()
            ->each(function ($item) {
                $nombre = $item->paciente->usuario->nombre . ' ' . $item->paciente->usuario->apPaterno;
                $this->command->info("  - {$nombre}: {$item->total} observaciones");
            });
    }

    /**
     * Crear médicos de prueba si no existen
     */
    private function crearMedicos()
    {
        $medicosData = [
            ['correo' => 'medico.seg1@clinica.com', 'nombre' => 'Roberto', 'apPaterno' => 'Sánchez', 'apMaterno' => 'López', 'especialidad' => 'Cardiología', 'cedula' => 'SEG001'],
            ['correo' => 'medico.seg2@clinica.com', 'nombre' => 'Laura', 'apPaterno' => 'Martínez', 'apMaterno' => 'García', 'especialidad' => 'Medicina General', 'cedula' => 'SEG002'],
        ];

        $medicos = collect();
        foreach ($medicosData as $data) {
            $user = User::updateOrCreate(
                ['correo' => $data['correo']],
                [
                    'nombre' => $data['nombre'],
                    'apPaterno' => $data['apPaterno'],
                    'apMaterno' => $data['apMaterno'],
                    'contrasena' => Hash::make('medico123'),
                    'rol' => 'medico',
                    'activo' => true,
                ]
            );
            $medico = Medico::updateOrCreate(
                ['id_usuario' => $user->id_usuario],
                [
                    'especialidad' => $data['especialidad'],
                    'cedula_profesional' => $data['cedula'],
                    'fecha_nacimiento' => Carbon::now()->subYears(rand(30, 60))->format('Y-m-d'),
                ]
            );
            $medicos->push($medico);
        }

        return $medicos;
    }

    /**
     * Crear pacientes de prueba si no existen
     */
    private function crearPacientes()
    {
        $pacientesData = [
            ['correo' => 'paciente.seg1@email.com', 'nombre' => 'Carlos', 'apPaterno' => 'Ramírez', 'apMaterno' => 'Torres', 'sexo' => 'masculino', 'fecha_nacimiento' => '1980-05-20'],
            ['correo' => 'paciente.seg2@email.com', 'nombre' => 'Sofía', 'apPaterno' => 'Hernández', 'apMaterno' => 'Díaz', 'sexo' => 'femenino', 'fecha_nacimiento' => '1975-08-15'],
        ];

        $pacientes = collect();
        foreach ($pacientesData as $data) {
            $user = User::updateOrCreate(
                ['correo' => $data['correo']],
                [
                    'nombre' => $data['nombre'],
                    'apPaterno' => $data['apPaterno'],
                    'apMaterno' => $data['apMaterno'],
                    'contrasena' => Hash::make('paciente123'),
                    'rol' => 'paciente',
                    'activo' => true,
                ]
            );
            $paciente = Paciente::updateOrCreate(
                ['id_usuario' => $user->id_usuario],
                [
                    'fecha_nacimiento' => $data['fecha_nacimiento'],
                    'sexo' => $data['sexo'],
                ]
            );
            $pacientes->push($paciente);
        }

        return $pacientes;
    }
}

