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
        Schema::create('respuestas', function (Blueprint $table) {
            $table->id('id_respuesta');
            $table->unsignedBigInteger('id_pregunta');
            $table->unsignedBigInteger('id_usuario');
            $table->text('respuesta');
            $table->date('fecha');
            
            $table->foreign('id_pregunta')->references('id_pregunta')->on('preguntas')->onDelete('cascade');
            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->onDelete('cascade');
            $table->index('id_pregunta');
            $table->index('id_usuario');
        });
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
