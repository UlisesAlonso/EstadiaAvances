<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class CreateAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear nuevo administrador
        $admin = User::updateOrCreate(
            ['correo' => 'admin@cardio.com'],
            [
                'nombre' => 'Administrador',
                'apPaterno' => 'Cardio',
                'apMaterno' => 'Vida',
                'contrasena' => Hash::make('admin123'),
                'rol' => 'administrador',
                'activo' => true,
            ]
        );

        $this->command->info('¡Administrador creado exitosamente!');
        $this->command->info('Correo: ' . $admin->correo);
        $this->command->info('Contraseña: admin123');
        $this->command->warn('Por favor, cambia la contraseña después del primer inicio de sesión.');
    }
}

