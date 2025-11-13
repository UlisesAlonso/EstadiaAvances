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
        Schema::create('actividades', function (Blueprint $table) {
            $table->id('id_actividad');
            $table->string('nombre', 100);
            $table->text('descripcion');
            $table->text('instrucciones')->nullable();
            $table->date('fecha_asignacion')->nullable();
            $table->date('fecha_limite')->nullable();
            $table->string('periodicidad', 50)->nullable();
            $table->unsignedBigInteger('id_paciente')->nullable();
            $table->unsignedBigInteger('id_medico')->nullable();
            $table->boolean('completada')->default(false);
            $table->text('comentarios_paciente')->nullable();
            $table->text('comentarios_medico')->nullable();
            $table->timestamps();
            
            $table->foreign('id_paciente')->references('id_paciente')->on('pacientes')->onDelete('cascade');
            $table->foreign('id_medico')->references('id_medico')->on('medicos')->onDelete('cascade');
            $table->index('id_paciente');
            $table->index('id_medico');
            $table->index('fecha_asignacion');
            $table->index('fecha_limite');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actividades');
    }
};
