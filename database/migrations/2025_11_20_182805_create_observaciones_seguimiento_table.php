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
        if (!Schema::hasTable('observaciones_seguimiento')) {
            Schema::create('observaciones_seguimiento', function (Blueprint $table) {
                $table->id('id_observacion');
                $table->unsignedBigInteger('id_paciente');
                $table->unsignedBigInteger('id_medico');
                $table->text('observacion');
                $table->date('fecha_observacion');
                $table->string('tipo', 100)->nullable()->comment('Tipo de observación: general, evolucion, alerta, etc.');
                $table->timestamp('fecha_creacion')->useCurrent();
                
                $table->foreign('id_paciente')->references('id_paciente')->on('pacientes')->onDelete('cascade');
                $table->foreign('id_medico')->references('id_medico')->on('medicos')->onDelete('cascade');
                
                $table->index('id_paciente');
                $table->index('id_medico');
                $table->index('fecha_observacion');
                $table->index('tipo');
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
        Schema::dropIfExists('observaciones_seguimiento');
    }
};
