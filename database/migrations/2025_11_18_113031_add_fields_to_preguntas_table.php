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
        Schema::table('preguntas', function (Blueprint $table) {
            $table->text('descripcion')->nullable()->after('texto');
            $table->string('especialidad_medica', 100)->nullable()->after('categoria');
            $table->date('fecha_asignacion')->nullable()->after('especialidad_medica');
            $table->unsignedBigInteger('id_diagnostico')->nullable()->after('fecha_asignacion');
            $table->unsignedBigInteger('id_tratamiento')->nullable()->after('id_diagnostico');
            $table->unsignedBigInteger('id_paciente')->nullable()->after('id_tratamiento');
            $table->unsignedBigInteger('id_medico')->nullable()->after('id_paciente');
            $table->json('opciones')->nullable()->after('id_medico'); // Para preguntas de opción múltiple
            $table->string('estado', 20)->default('activa')->after('opciones'); // activa, inactiva
            $table->timestamp('fecha_creacion')->nullable()->after('estado');
            
            // Foreign keys
            $table->foreign('id_diagnostico')->references('id_diagnostico')->on('diagnosticos')->onDelete('set null');
            $table->foreign('id_tratamiento')->references('id_tratamiento')->on('tratamientos')->onDelete('set null');
            $table->foreign('id_paciente')->references('id_paciente')->on('pacientes')->onDelete('set null');
            $table->foreign('id_medico')->references('id_medico')->on('medicos')->onDelete('set null');
            
            // Índices
            $table->index('id_diagnostico');
            $table->index('id_tratamiento');
            $table->index('id_paciente');
            $table->index('id_medico');
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('preguntas', function (Blueprint $table) {
            $table->dropForeign(['id_diagnostico']);
            $table->dropForeign(['id_tratamiento']);
            $table->dropForeign(['id_paciente']);
            $table->dropForeign(['id_medico']);
            
            $table->dropColumn([
                'descripcion',
                'especialidad_medica',
                'fecha_asignacion',
                'id_diagnostico',
                'id_tratamiento',
                'id_paciente',
                'id_medico',
                'opciones',
                'estado',
                'fecha_creacion'
            ]);
        });
    }
};
