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
        if (!Schema::hasTable('preguntas')) {
            Schema::create('preguntas', function (Blueprint $table) {
            $table->id('id_pregunta');
            $table->text('descripcion'); // Descripción de la pregunta
            $table->enum('tipo', ['abierta', 'opcion_multiple']); // Tipo: abierta u opción múltiple
            $table->text('opciones_multiple')->nullable(); // JSON con opciones si es opción múltiple
            $table->string('especialidad_medica', 100)->nullable(); // Especialidad médica relacionada
            $table->date('fecha_asignacion'); // Fecha de asignación
            $table->unsignedBigInteger('id_diagnostico')->nullable(); // Diagnóstico vinculado
            $table->unsignedBigInteger('id_tratamiento')->nullable(); // Tratamiento vinculado
            $table->unsignedBigInteger('id_paciente')->nullable(); // Paciente destinatario (null = todos)
            $table->unsignedBigInteger('id_medico'); // Médico/Administrador que crea
            $table->timestamp('fecha_creacion')->useCurrent(); // Fecha de creación
            $table->boolean('activa')->default(true); // Estado activo/inactivo
            
            // Foreign keys
            $table->foreign('id_diagnostico')->references('id_diagnostico')->on('diagnosticos')->onDelete('set null');
            $table->foreign('id_tratamiento')->references('id_tratamiento')->on('tratamientos')->onDelete('set null');
            $table->foreign('id_paciente')->references('id_paciente')->on('pacientes')->onDelete('cascade');
            $table->foreign('id_medico')->references('id_medico')->on('medicos')->onDelete('cascade');
            
            // Índices
            $table->index('id_paciente');
            $table->index('id_medico');
            $table->index('fecha_asignacion');
            $table->index('activa');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('preguntas');
    }
};
