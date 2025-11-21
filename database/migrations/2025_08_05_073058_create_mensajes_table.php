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
        Schema::create('mensajes', function (Blueprint $table) {
            $table->id('id_mensaje');
            $table->unsignedBigInteger('remitente_id');
            $table->unsignedBigInteger('destinatario_id');
            $table->text('mensaje');
            $table->dateTime('fecha_envio');
            
            $table->foreign('remitente_id')->references('id_usuario')->on('usuarios')->onDelete('cascade');
            $table->foreign('destinatario_id')->references('id_usuario')->on('usuarios')->onDelete('cascade');
            $table->index('remitente_id');
            $table->index('destinatario_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mensajes');
    }
};
