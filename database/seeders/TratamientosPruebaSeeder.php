<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Paciente;
use App\Models\Medico;
use App\Models\Tratamiento;
use App\Models\Diagnostico;
use App\Models\CatalogoDiagnostico;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TratamientosPruebaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creando tratamientos de prueba...');

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

        // Obtener o crear diagnósticos de ejemplo
        $diagnosticos = $this->obtenerDiagnosticos($pacientes);

        // Tratamientos de ejemplo con variedad
        $tratamientosData = [
            // Tratamientos activos
            ['nombre' => 'Metformina', 'dosis' => '500mg', 'frecuencia' => '2 veces al día', 'duracion' => '3 meses', 'activo' => true],
            ['nombre' => 'Losartán', 'dosis' => '50mg', 'frecuencia' => '1 vez al día', 'duracion' => '6 meses', 'activo' => true],
            ['nombre' => 'Atorvastatina', 'dosis' => '20mg', 'frecuencia' => '1 vez al día', 'duracion' => 'Indefinido', 'activo' => true],
            ['nombre' => 'Aspirina', 'dosis' => '100mg', 'frecuencia' => '1 vez al día', 'duracion' => 'Indefinido', 'activo' => true],
            ['nombre' => 'Amlodipino', 'dosis' => '5mg', 'frecuencia' => '1 vez al día', 'duracion' => '4 meses', 'activo' => true],
            ['nombre' => 'Omeprazol', 'dosis' => '20mg', 'frecuencia' => '1 vez al día', 'duracion' => '2 meses', 'activo' => true],
            ['nombre' => 'Metoprolol', 'dosis' => '50mg', 'frecuencia' => '2 veces al día', 'duracion' => '6 meses', 'activo' => true],
            ['nombre' => 'Enalapril', 'dosis' => '10mg', 'frecuencia' => '1 vez al día', 'duracion' => '5 meses', 'activo' => true],
            ['nombre' => 'Furosemida', 'dosis' => '40mg', 'frecuencia' => '1 vez al día', 'duracion' => '3 meses', 'activo' => true],
            ['nombre' => 'Clopidogrel', 'dosis' => '75mg', 'frecuencia' => '1 vez al día', 'duracion' => 'Indefinido', 'activo' => true],
            
            // Tratamientos inactivos (finalizados)
            ['nombre' => 'Amoxicilina', 'dosis' => '500mg', 'frecuencia' => '3 veces al día', 'duracion' => '7 días', 'activo' => false],
            ['nombre' => 'Ibuprofeno', 'dosis' => '400mg', 'frecuencia' => '3 veces al día', 'duracion' => '5 días', 'activo' => false],
            ['nombre' => 'Paracetamol', 'dosis' => '500mg', 'frecuencia' => 'Cada 8 horas', 'duracion' => '3 días', 'activo' => false],
            ['nombre' => 'Azitromicina', 'dosis' => '500mg', 'frecuencia' => '1 vez al día', 'duracion' => '5 días', 'activo' => false],
            ['nombre' => 'Ciprofloxacino', 'dosis' => '500mg', 'frecuencia' => '2 veces al día', 'duracion' => '7 días', 'activo' => false],
        ];

        $observaciones = [
            'Tomar con alimentos',
            'Tomar en ayunas',
            'Evitar alcohol durante el tratamiento',
            'Monitorear presión arterial semanalmente',
            'Continuar hasta nueva indicación médica',
            'Suspender si aparecen efectos secundarios',
            'Tomar con abundante agua',
            'No tomar con leche',
            'Evitar exposición al sol',
            'Combinar con dieta baja en sal',
            null, // Algunos sin observaciones
        ];

        $this->command->info('Generando 60 tratamientos de prueba...');
        $contador = 0;

        for ($i = 0; $i < 60; $i++) {
            $tratamientoData = $tratamientosData[array_rand($tratamientosData)];
            $medico = $medicos->random();
            $paciente = $pacientes->random();
            $diagnostico = $diagnosticos->random();
            
            // Fechas distribuidas en los últimos 6 meses y próximos
            $fechaInicio = Carbon::now()->subDays(rand(0, 180))->addHours(rand(9, 17));
            if (rand(0, 1)) { // 50% de probabilidad de que sea futuro
                $fechaInicio = Carbon::now()->addDays(rand(1, 90))->addHours(rand(9, 17));
            }

            Tratamiento::create([
                'id_paciente' => $paciente->id_paciente,
                'id_medico' => $medico->id_medico,
                'id_diagnostico' => $diagnostico->id_diagnostico,
                'nombre' => $tratamientoData['nombre'],
                'dosis' => $tratamientoData['dosis'],
                'frecuencia' => $tratamientoData['frecuencia'],
                'duracion' => $tratamientoData['duracion'],
                'observaciones' => $observaciones[array_rand($observaciones)],
                'fecha_inicio' => $fechaInicio,
                'activo' => $tratamientoData['activo'],
            ]);

            $contador++;
        }

        $this->command->info("✓ Se crearon {$contador} tratamientos de prueba exitosamente!");

        // Mostrar estadísticas
        $this->command->info('Distribución de tratamientos:');
        $activos = Tratamiento::where('activo', true)->count();
        $inactivos = Tratamiento::where('activo', false)->count();
        $this->command->info("  - Activos: {$activos} tratamientos");
        $this->command->info("  - Inactivos: {$inactivos} tratamientos");

        $this->command->info('Distribución por médico:');
        Tratamiento::select('id_medico', \DB::raw('count(*) as total'))
            ->groupBy('id_medico')
            ->with('medico.usuario')
            ->get()
            ->each(function ($item) {
                $nombre = $item->medico->usuario->nombre . ' ' . $item->medico->usuario->apPaterno;
                $this->command->info("  - {$nombre}: {$item->total} tratamientos");
            });
    }

    /**
     * Crear médicos de prueba si no existen
     */
    private function crearMedicos()
    {
        $medicosData = [
            ['correo' => 'medico.trat1@clinica.com', 'nombre' => 'Carlos', 'apPaterno' => 'Méndez', 'apMaterno' => 'García', 'especialidad' => 'Cardiología', 'cedula' => 'TRAT001'],
            ['correo' => 'medico.trat2@clinica.com', 'nombre' => 'Ana', 'apPaterno' => 'López', 'apMaterno' => 'Martínez', 'especialidad' => 'Medicina General', 'cedula' => 'TRAT002'],
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
            ['correo' => 'paciente.trat1@email.com', 'nombre' => 'María', 'apPaterno' => 'González', 'apMaterno' => 'Sánchez', 'sexo' => 'femenino', 'fecha_nacimiento' => '1985-03-15'],
            ['correo' => 'paciente.trat2@email.com', 'nombre' => 'Juan', 'apPaterno' => 'Rodríguez', 'apMaterno' => 'Pérez', 'sexo' => 'masculino', 'fecha_nacimiento' => '1978-07-22'],
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

    /**
     * Obtener o crear diagnósticos para los pacientes
     */
    private function obtenerDiagnosticos($pacientes)
    {
        // Obtener médicos para asignar a los diagnósticos
        $medicos = Medico::all();
        if ($medicos->isEmpty()) {
            $medicos = $this->crearMedicos();
        }

        // Obtener catálogo de diagnósticos existente
        $catalogoDiagnosticos = CatalogoDiagnostico::take(5)->get();
        
        // Obtener un usuario administrador para crear el catálogo si no existe
        $adminUser = User::where('rol', 'administrador')->first();
        if (!$adminUser) {
            $adminUser = User::first();
        }

        // Si no hay catálogo, crear algunos diagnósticos de ejemplo
        if ($catalogoDiagnosticos->isEmpty() && $adminUser) {
            $catalogoData = [
                ['codigo' => 'I10', 'descripcion_clinica' => 'Hipertensión esencial', 'categoria_medica' => 'Cardiovascular'],
                ['codigo' => 'E11', 'descripcion_clinica' => 'Diabetes mellitus tipo 2', 'categoria_medica' => 'Endocrinología'],
                ['codigo' => 'I25', 'descripcion_clinica' => 'Enfermedad cardíaca isquémica crónica', 'categoria_medica' => 'Cardiovascular'],
                ['codigo' => 'E78', 'descripcion_clinica' => 'Trastornos del metabolismo de las lipoproteínas', 'categoria_medica' => 'Endocrinología'],
                ['codigo' => 'I50', 'descripcion_clinica' => 'Insuficiencia cardíaca', 'categoria_medica' => 'Cardiovascular'],
            ];

            foreach ($catalogoData as $data) {
                CatalogoDiagnostico::create([
                    'codigo' => $data['codigo'],
                    'descripcion_clinica' => $data['descripcion_clinica'],
                    'categoria_medica' => $data['categoria_medica'],
                    'id_usuario_creador' => $adminUser->id_usuario,
                    'fecha_creacion' => Carbon::now(),
                ]);
            }
            $catalogoDiagnosticos = CatalogoDiagnostico::all();
        }

        // Crear diagnósticos basados en el catálogo
        $diagnosticos = collect();
        foreach ($pacientes as $paciente) {
            $medico = $medicos->random();
            
            if (!$catalogoDiagnosticos->isEmpty()) {
                $catalogo = $catalogoDiagnosticos->random();
                $diagnostico = Diagnostico::firstOrCreate(
                    [
                        'id_paciente' => $paciente->id_paciente,
                        'id_PDiag' => $catalogo->id_diagnostico,
                    ],
                    [
                        'id_medico' => $medico->id_medico,
                        'fecha' => Carbon::now()->subDays(rand(30, 180)),
                        'descripcion' => 'Diagnóstico de prueba para tratamiento',
                    ]
                );
            } else {
                // Si no hay catálogo, crear diagnóstico sin catálogo (aunque la FK requiere catálogo)
                // En este caso, usaremos el primer catálogo disponible o crearemos uno mínimo
                if ($catalogoDiagnosticos->isEmpty() && $adminUser) {
                    $catalogo = CatalogoDiagnostico::create([
                        'codigo' => 'Z00',
                        'descripcion_clinica' => 'Examen médico general',
                        'categoria_medica' => 'General',
                        'id_usuario_creador' => $adminUser->id_usuario,
                        'fecha_creacion' => Carbon::now(),
                    ]);
                } else {
                    $catalogo = $catalogoDiagnosticos->first();
                }
                
                $diagnostico = Diagnostico::create([
                    'id_paciente' => $paciente->id_paciente,
                    'id_medico' => $medico->id_medico,
                    'id_PDiag' => $catalogo->id_diagnostico,
                    'fecha' => Carbon::now()->subDays(rand(30, 180)),
                    'descripcion' => 'Diagnóstico de prueba para tratamiento',
                ]);
            }
            
            $diagnosticos->push($diagnostico);
        }

        return $diagnosticos;
    }
}

