<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('actividades', function (Blueprint $table) {
            // Agregar campos para la gestión completa de actividades
            $table->text('instrucciones')->nullable()->after('descripcion');
            $table->date('fecha_asignacion')->nullable()->after('instrucciones');
            $table->date('fecha_limite')->nullable()->after('fecha_asignacion');
            $table->string('periodicidad', 50)->nullable()->after('fecha_limite');
            $table->unsignedBigInteger('id_paciente')->nullable()->after('periodicidad');
            $table->unsignedBigInteger('id_medico')->nullable()->after('id_paciente');
            $table->boolean('completada')->default(false)->after('id_medico');
            $table->text('comentarios_paciente')->nullable()->after('completada');
            $table->timestamp('created_at')->nullable()->after('comentarios_paciente');
            $table->timestamp('updated_at')->nullable()->after('created_at');
            
            // Agregar índices
            $table->index('id_paciente');
            $table->index('id_medico');
            $table->index('fecha_asignacion');
            $table->index('fecha_limite');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('actividades', function (Blueprint $table) {
            $table->dropColumn([
                'instrucciones',
                'fecha_asignacion',
                'fecha_limite',
                'periodicidad',
                'id_paciente',
                'id_medico',
                'completada',
                'comentarios_paciente',
                'created_at',
                'updated_at'
            ]);
        });
    }
};
