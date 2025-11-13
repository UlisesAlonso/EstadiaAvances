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
        Schema::create('diagnosticos', function (Blueprint $table) {
            $table->id('id_diagnostico');
            $table->unsignedBigInteger('id_paciente');
            $table->unsignedBigInteger('id_medico');
            $table->unsignedBigInteger('id_PDiag');
            $table->date('fecha');
            $table->text('descripcion');
            $table->timestamps();
            
            $table->foreign('id_paciente')->references('id_paciente')->on('pacientes')->onDelete('cascade');
            $table->foreign('id_medico')->references('id_medico')->on('medicos')->onDelete('cascade');
            $table->foreign('id_PDiag')->references('id_diagnostico')->on('catalogo_diagnosticos')->onDelete('cascade');
            $table->index('id_paciente');
            $table->index('id_medico');
            $table->index('id_PDiag');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diagnosticos');
    }
};
