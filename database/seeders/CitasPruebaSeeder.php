<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Cita;
use App\Models\Paciente;
use App\Models\Medico;
use App\Models\User;
use Carbon\Carbon;

class CitasPruebaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener pacientes y médicos existentes
        $pacientes = Paciente::with('usuario')->get();
        $medicos = Medico::with('usuario')->get();

        // Si no hay suficientes médicos, crear algunos adicionales
        if ($medicos->count() < 5) {
            $this->command->info('Creando médicos adicionales con diferentes especialidades...');
            $this->crearMedicosAdicionales();
            $medicos = Medico::with('usuario')->get();
        }

        // Si no hay suficientes pacientes, crear algunos adicionales
        if ($pacientes->count() < 10) {
            $this->command->info('Creando pacientes adicionales...');
            $this->crearPacientesAdicionales();
            $pacientes = Paciente::with('usuario')->get();
        }

        if ($pacientes->isEmpty() || $medicos->isEmpty()) {
            $this->command->warn('No hay pacientes o médicos en la base de datos. Por favor ejecuta primero UserSeeder.');
            return;
        }

        // Especialidades médicas
        $especialidades = [
            'Cardiología',
            'Medicina General',
            'Endocrinología',
            'Neurología',
            'Oncología',
            'Pediatría',
            'Ginecología',
            'Dermatología'
        ];

        // Estados de citas
        $estados = ['pendiente', 'confirmada', 'completada', 'cancelada'];

        // Motivos de consulta
        $motivos = [
            'Consulta de rutina',
            'Revisión de presión arterial',
            'Control de medicamentos',
            'Dolor en el pecho',
            'Palpitaciones',
            'Falta de aire',
            'Fatiga constante',
            'Mareos frecuentes',
            'Control post-operatorio',
            'Seguimiento de tratamiento',
            'Evaluación cardiovascular',
            'Consulta de emergencia',
            'Revisión de análisis clínicos',
            'Consulta preventiva',
            'Control de diabetes'
        ];

        // Generar citas para los últimos 3 meses
        $fechaInicio = Carbon::now()->subMonths(3);
        $fechaFin = Carbon::now()->addMonths(1);
        
        $citasCreadas = 0;
        $totalCitas = 80; // Total de citas a crear

        $this->command->info('Generando ' . $totalCitas . ' citas de prueba...');

        for ($i = 0; $i < $totalCitas; $i++) {
            // Seleccionar paciente y médico aleatorios
            $paciente = $pacientes->random();
            $medico = $medicos->random();

            // Generar fecha aleatoria en el rango
            $diasAleatorios = rand(0, $fechaInicio->diffInDays($fechaFin));
            $fecha = $fechaInicio->copy()->addDays($diasAleatorios);
            
            // Ajustar hora (entre 8:00 y 18:00)
            $hora = rand(8, 18);
            $minuto = rand(0, 1) * 30; // 00 o 30
            $fecha->setTime($hora, $minuto, 0);

            // Seleccionar estado (distribución realista)
            $randEstado = rand(1, 100);
            if ($randEstado <= 20) {
                $estado = 'pendiente';
            } elseif ($randEstado <= 50) {
                $estado = 'confirmada';
            } elseif ($randEstado <= 85) {
                $estado = 'completada';
            } else {
                $estado = 'cancelada';
            }

            // Si la fecha es futura, no puede estar completada
            if ($fecha->isFuture() && $estado == 'completada') {
                $estado = rand(0, 1) ? 'pendiente' : 'confirmada';
            }

            // Si la fecha es muy pasada, probablemente está completada
            if ($fecha->isPast() && $fecha->diffInDays(now()) > 30 && $estado == 'pendiente') {
                $estado = rand(0, 1) ? 'completada' : 'cancelada';
            }

            // Seleccionar especialidad aleatoria
            $especialidad = $especialidades[array_rand($especialidades)];

            // Seleccionar motivo aleatorio
            $motivo = $motivos[array_rand($motivos)];

            // Crear la cita
            Cita::create([
                'id_paciente' => $paciente->id_paciente,
                'id_medico' => $medico->id_medico,
                'fecha' => $fecha,
                'motivo' => $motivo,
                'estado' => $estado,
                'especialidad_medica' => $especialidad,
                'observaciones_clinicas' => rand(0, 1) ? 'Paciente en buen estado general. Se recomienda seguimiento.' : null,
            ]);

            $citasCreadas++;
        }

        $this->command->info("✓ Se crearon {$citasCreadas} citas de prueba exitosamente!");
        $this->command->info("\nDistribución de citas:");
        
        $distribucion = Cita::groupBy('estado')
            ->selectRaw('estado, count(*) as total')
            ->get();
        
        foreach ($distribucion as $dist) {
            $this->command->info("  - {$dist->estado}: {$dist->total} citas");
        }

        $this->command->info("\nDistribución por especialidad:");
        $distEspecialidad = Cita::whereNotNull('especialidad_medica')
            ->groupBy('especialidad_medica')
            ->selectRaw('especialidad_medica, count(*) as total')
            ->get();
        
        foreach ($distEspecialidad as $dist) {
            $this->command->info("  - {$dist->especialidad_medica}: {$dist->total} citas");
        }
    }

    /**
     * Crear médicos adicionales con diferentes especialidades
     */
    private function crearMedicosAdicionales(): void
    {
        $medicosData = [
            ['nombre' => 'Roberto', 'apellido' => 'Sánchez', 'especialidad' => 'Medicina General', 'correo' => 'roberto.sanchez@clinica.com'],
            ['nombre' => 'Laura', 'apellido' => 'Martínez', 'especialidad' => 'Endocrinología', 'correo' => 'laura.martinez@clinica.com'],
            ['nombre' => 'Pedro', 'apellido' => 'González', 'especialidad' => 'Neurología', 'correo' => 'pedro.gonzalez@clinica.com'],
            ['nombre' => 'Carmen', 'apellido' => 'Fernández', 'especialidad' => 'Oncología', 'correo' => 'carmen.fernandez@clinica.com'],
            ['nombre' => 'Luis', 'apellido' => 'Ramírez', 'especialidad' => 'Pediatría', 'correo' => 'luis.ramirez@clinica.com'],
            ['nombre' => 'Patricia', 'apellido' => 'Morales', 'especialidad' => 'Ginecología', 'correo' => 'patricia.morales@clinica.com'],
            ['nombre' => 'Fernando', 'apellido' => 'Castro', 'especialidad' => 'Dermatología', 'correo' => 'fernando.castro@clinica.com'],
        ];

        foreach ($medicosData as $medicoData) {
            $user = User::firstOrCreate(
                ['correo' => $medicoData['correo']],
                [
                    'nombre' => $medicoData['nombre'],
                    'apPaterno' => $medicoData['apellido'],
                    'apMaterno' => '',
                    'contrasena' => Hash::make('medico123'),
                    'rol' => 'medico',
                    'activo' => true,
                ]
            );

            Medico::firstOrCreate(
                ['id_usuario' => $user->id_usuario],
                [
                    'especialidad' => $medicoData['especialidad'],
                    'cedula_profesional' => strtoupper(substr($medicoData['especialidad'], 0, 3)) . rand(100, 999),
                ]
            );
        }
    }

    /**
     * Crear pacientes adicionales
     */
    private function crearPacientesAdicionales(): void
    {
        $pacientesData = [
            ['nombre' => 'José', 'apellido' => 'Hernández', 'correo' => 'jose.hernandez@email.com', 'fecha_nac' => '1980-05-10', 'sexo' => 'masculino'],
            ['nombre' => 'Rosa', 'apellido' => 'Jiménez', 'correo' => 'rosa.jimenez@email.com', 'fecha_nac' => '1975-09-20', 'sexo' => 'femenino'],
            ['nombre' => 'Miguel', 'apellido' => 'Pérez', 'correo' => 'miguel.perez@email.com', 'fecha_nac' => '1988-12-03', 'sexo' => 'masculino'],
            ['nombre' => 'Sofía', 'apellido' => 'Díaz', 'correo' => 'sofia.diaz@email.com', 'fecha_nac' => '1990-02-14', 'sexo' => 'femenino'],
            ['nombre' => 'Ricardo', 'apellido' => 'Moreno', 'correo' => 'ricardo.moreno@email.com', 'fecha_nac' => '1983-08-25', 'sexo' => 'masculino'],
            ['nombre' => 'Elena', 'apellido' => 'Vargas', 'correo' => 'elena.vargas@email.com', 'fecha_nac' => '1979-06-18', 'sexo' => 'femenino'],
            ['nombre' => 'Daniel', 'apellido' => 'Ruiz', 'correo' => 'daniel.ruiz@email.com', 'fecha_nac' => '1995-04-07', 'sexo' => 'masculino'],
            ['nombre' => 'Andrea', 'apellido' => 'Cruz', 'correo' => 'andrea.cruz@email.com', 'fecha_nac' => '1987-10-30', 'sexo' => 'femenino'],
        ];

        foreach ($pacientesData as $pacienteData) {
            $user = User::firstOrCreate(
                ['correo' => $pacienteData['correo']],
                [
                    'nombre' => $pacienteData['nombre'],
                    'apPaterno' => $pacienteData['apellido'],
                    'apMaterno' => '',
                    'contrasena' => Hash::make('paciente123'),
                    'rol' => 'paciente',
                    'activo' => true,
                ]
            );

            Paciente::firstOrCreate(
                ['id_usuario' => $user->id_usuario],
                [
                    'fecha_nacimiento' => $pacienteData['fecha_nac'],
                    'sexo' => $pacienteData['sexo'],
                ]
            );
        }
    }
}

