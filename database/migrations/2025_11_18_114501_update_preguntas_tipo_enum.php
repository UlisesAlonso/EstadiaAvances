<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Actualizar el enum de tipo de pregunta de 'cerrada' a 'opcion_multiple'
        DB::statement("ALTER TABLE preguntas MODIFY COLUMN tipo ENUM('abierta', 'opcion_multiple') NOT NULL");
        
        // Si hay registros con 'cerrada', actualizarlos a 'opcion_multiple'
        DB::table('preguntas')
            ->where('tipo', 'cerrada')
            ->update(['tipo' => 'opcion_multiple']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revertir a 'cerrada' si es necesario
        DB::statement("ALTER TABLE preguntas MODIFY COLUMN tipo ENUM('abierta', 'cerrada') NOT NULL");
    }
};
