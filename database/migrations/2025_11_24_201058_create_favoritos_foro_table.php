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
        Schema::create('favoritos_foro', function (Blueprint $table) {
            $table->id('id_favorito');
            $table->unsignedBigInteger('id_publicacion');
            $table->unsignedBigInteger('id_paciente');
            $table->timestamps();
            
            // Índices
            $table->index('id_publicacion');
            $table->index('id_paciente');
            
            // Unique constraint: un paciente solo puede marcar una vez como favorito
            $table->unique(['id_publicacion', 'id_paciente'], 'unique_favorito_paciente_publicacion');
            
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
        Schema::dropIfExists('favoritos_foro');
    }
};
