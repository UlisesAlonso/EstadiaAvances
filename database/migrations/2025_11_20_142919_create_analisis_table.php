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
        if (!Schema::hasTable('analisis')) {
            Schema::create('analisis', function (Blueprint $table) {
                $table->id('id_analisis');
                $table->string('tipo_estudio', 255);
                $table->text('descripcion');
                $table->date('fecha_analisis');
                $table->unsignedBigInteger('id_paciente');
                $table->unsignedBigInteger('id_medico');
                $table->text('valores_obtenidos')->nullable();
                $table->text('observaciones_clinicas')->nullable();
                $table->timestamp('fecha_creacion')->useCurrent();
                
                $table->foreign('id_paciente')->references('id_paciente')->on('pacientes')->onDelete('cascade');
                $table->foreign('id_medico')->references('id_medico')->on('medicos')->onDelete('cascade');
                
                $table->index('id_paciente');
                $table->index('id_medico');
                $table->index('fecha_analisis');
                $table->index('tipo_estudio');
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
        Schema::dropIfExists('analisis');
    }
};
