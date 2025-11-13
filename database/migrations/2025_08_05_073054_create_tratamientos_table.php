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
        Schema::create('tratamientos', function (Blueprint $table) {
            $table->id('id_tratamiento');
            $table->unsignedBigInteger('id_paciente');
            $table->unsignedBigInteger('id_medico');
            $table->unsignedBigInteger('id_diagnostico')->nullable();
            $table->string('nombre', 100);
            $table->string('dosis', 100);
            $table->string('frecuencia', 100);
            $table->string('duracion', 100);
            $table->text('observaciones')->nullable();
            $table->date('fecha_inicio');
            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            $table->foreign('id_paciente')->references('id_paciente')->on('pacientes')->onDelete('cascade');
            $table->foreign('id_medico')->references('id_medico')->on('medicos')->onDelete('cascade');
            $table->foreign('id_diagnostico')->references('id_diagnostico')->on('diagnosticos')->onDelete('set null');
            $table->index('id_paciente');
            $table->index('id_medico');
            $table->index('id_diagnostico');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tratamientos');
    }
};
