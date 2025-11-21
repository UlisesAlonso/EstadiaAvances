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
        if (!Schema::hasTable('respuestas')) {
            Schema::create('respuestas', function (Blueprint $table) {
            $table->id('id_respuesta');
            $table->unsignedBigInteger('id_pregunta');
            $table->unsignedBigInteger('id_usuario'); // ID del paciente que responde
            $table->text('respuesta'); // Respuesta dada
            $table->dateTime('fecha_respuesta')->useCurrent(); // Fecha y hora de respuesta
            $table->boolean('cumplimiento')->default(false); // Indicador de cumplimiento
            
            $table->foreign('id_pregunta')->references('id_pregunta')->on('preguntas')->onDelete('cascade');
            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->onDelete('cascade');
            $table->index('id_pregunta');
            $table->index('id_usuario');
            $table->index('fecha_respuesta');
            $table->index('cumplimiento');
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
        Schema::dropIfExists('respuestas');
    }
};
