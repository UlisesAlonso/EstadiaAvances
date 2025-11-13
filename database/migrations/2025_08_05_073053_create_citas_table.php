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
        Schema::create('citas', function (Blueprint $table) {
            $table->id('id_cita');
            $table->unsignedBigInteger('id_paciente');
            $table->unsignedBigInteger('id_medico');
            $table->dateTime('fecha');
            $table->text('motivo');
            $table->text('observaciones_clinicas')->nullable();
            $table->string('especialidad_medica')->nullable();
            $table->enum('estado', ['pendiente', 'confirmada', 'completada', 'cancelada'])->default('pendiente');
            $table->timestamps();
            
            $table->foreign('id_paciente')->references('id_paciente')->on('pacientes')->onDelete('cascade');
            $table->foreign('id_medico')->references('id_medico')->on('medicos')->onDelete('cascade');
            $table->index('id_paciente');
            $table->index('id_medico');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
