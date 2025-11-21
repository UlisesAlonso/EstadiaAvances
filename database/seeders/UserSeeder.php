<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Paciente;
use App\Models\Medico;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear o actualizar Administrador
        $admin = User::updateOrCreate(
            ['correo' => 'admin@sistema.com'],
            [
                'nombre' => 'Administrador',
                'apPaterno' => 'Sistema',
                'apMaterno' => null,
            'contrasena' => Hash::make('admin123'),
            'rol' => 'administrador',
            'activo' => true,
            ]
        );

        // Crear o actualizar Médicos
        $medico1 = User::updateOrCreate(
            ['correo' => 'juan.mendez@clinica.com'],
            [
                'nombre' => 'Juan Carlos',
                'apPaterno' => 'Méndez',
                'apMaterno' => null,
            'contrasena' => Hash::make('medico123'),
            'rol' => 'medico',
            'activo' => true,
            ]
        );

        $medico2 = User::updateOrCreate(
            ['correo' => 'maria.rodriguez@clinica.com'],
            [
                'nombre' => 'María Elena',
                'apPaterno' => 'Rodríguez',
                'apMaterno' => null,
            'contrasena' => Hash::make('medico123'),
            'rol' => 'medico',
            'activo' => true,
            ]
        );

        // Crear o actualizar registros de médicos
        Medico::updateOrCreate(
            ['id_usuario' => $medico1->id_usuario],
            [
            'especialidad' => 'Cardiología',
            'cedula_profesional' => 'CARD001',
            ]
        );

        Medico::updateOrCreate(
            ['id_usuario' => $medico2->id_usuario],
            [
            'especialidad' => 'Cardiología',
            'cedula_profesional' => 'CARD002',
            ]
        );

        // Crear o actualizar Pacientes
        $paciente1 = User::updateOrCreate(
            ['correo' => 'ana.garcia@email.com'],
            [
                'nombre' => 'Ana Sofía',
                'apPaterno' => 'García',
                'apMaterno' => null,
            'contrasena' => Hash::make('paciente123'),
            'rol' => 'paciente',
            'activo' => true,
            ]
        );

        $paciente2 = User::updateOrCreate(
            ['correo' => 'carlos.lopez@email.com'],
            [
                'nombre' => 'Carlos Alberto',
                'apPaterno' => 'López',
                'apMaterno' => null,
            'contrasena' => Hash::make('paciente123'),
            'rol' => 'paciente',
            'activo' => true,
            ]
        );

        $paciente3 = User::updateOrCreate(
            ['correo' => 'maria.torres@email.com'],
            [
                'nombre' => 'María Isabel',
                'apPaterno' => 'Torres',
                'apMaterno' => null,
            'contrasena' => Hash::make('paciente123'),
            'rol' => 'paciente',
            'activo' => true,
            ]
        );

        // Crear o actualizar registros de pacientes
        Paciente::updateOrCreate(
            ['id_usuario' => $paciente1->id_usuario],
            [
            'fecha_nacimiento' => '1985-03-15',
                'sexo' => 'femenino',
            ]
        );

        Paciente::updateOrCreate(
            ['id_usuario' => $paciente2->id_usuario],
            [
            'fecha_nacimiento' => '1978-07-22',
                'sexo' => 'masculino',
            ]
        );

        Paciente::updateOrCreate(
            ['id_usuario' => $paciente3->id_usuario],
            [
            'fecha_nacimiento' => '1992-11-08',
                'sexo' => 'femenino',
            ]
        );

        $this->command->info('Usuarios de prueba creados exitosamente!');
        $this->command->info('Administrador: admin@sistema.com / admin123');
        $this->command->info('Médicos: juan.mendez@clinica.com / medico123');
        $this->command->info('Pacientes: ana.garcia@email.com / paciente123');
    }
}
