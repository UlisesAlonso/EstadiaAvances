<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('historial_clinico', function (Blueprint $table) {
            $table->id('id_historial');
            $table->unsignedBigInteger('id_paciente');
            $table->unsignedBigInteger('id_medico')->nullable();
            $table->unsignedBigInteger('id_diagnostico');
            $table->unsignedBigInteger('id_tratamiento');
            $table->text('observaciones')->nullable();
            $table->date('fecha_registro');
            $table->date('fecha_evento')->nullable();
            $table->text('resultados_analisis')->nullable();
            $table->json('archivos_adjuntos')->nullable();
            $table->enum('estado', ['activo', 'cerrado'])->default('activo');
            $table->timestamps();
            
            $table->foreign('id_paciente')->references('id_paciente')->on('pacientes')->onDelete('cascade');
            $table->foreign('id_medico')->references('id_medico')->on('medicos')->onDelete('set null');
            $table->foreign('id_diagnostico')->references('id_diagnostico')->on('diagnosticos')->onDelete('cascade');
            $table->foreign('id_tratamiento')->references('id_tratamiento')->on('tratamientos')->onDelete('cascade');
            $table->index('id_paciente');
            $table->index('id_diagnostico');
            $table->index('id_tratamiento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_clinico');
    }
};
