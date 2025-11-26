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
        Schema::create('comentarios_foro', function (Blueprint $table) {
            $table->id('id_comentario');
            $table->unsignedBigInteger('id_publicacion');
            $table->unsignedBigInteger('id_paciente');
            $table->text('contenido');
            $table->datetime('fecha_comentario');
            $table->timestamps();
            
            // Índices
            $table->index('id_publicacion');
            $table->index('id_paciente');
            $table->index('fecha_comentario');
            
            // Foreign keys
            $table->foreign('id_publicacion')
                  ->references('id_publicacion')
                  ->on('publicaciones_foro')
                  ->onDelete('cascade');
                  
            $table->foreign('id_paciente')
                  ->references('id_paciente')
                  ->on('pacientes')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comentarios_foro');
    }
};
