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
        Schema::create('publicaciones_foro', function (Blueprint $table) {
            $table->id('id_publicacion');
            $table->unsignedBigInteger('id_paciente');
            $table->string('titulo', 255);
            $table->text('contenido');
            $table->datetime('fecha_publicacion');
            $table->enum('estado', ['pendiente', 'aprobada', 'oculta'])->default('pendiente');
            $table->unsignedBigInteger('id_actividad')->nullable();
            $table->unsignedBigInteger('id_tratamiento')->nullable();
            $table->string('etiquetas', 500)->nullable()->comment('Etiquetas separadas por comas para búsqueda');
            $table->timestamps();
            
            // Índices
            $table->index('id_paciente');
            $table->index('fecha_publicacion');
            $table->index('estado');
            $table->index('id_actividad');
            $table->index('id_tratamiento');
            
            // Foreign keys
            $table->foreign('id_paciente')
                  ->references('id_paciente')
                  ->on('pacientes')
                  ->onDelete('cascade');
                  
            $table->foreign('id_actividad')
                  ->references('id_actividad')
                  ->on('actividades')
                  ->onDelete('set null');
                  
            $table->foreign('id_tratamiento')
                  ->references('id_tratamiento')
                  ->on('tratamientos')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('publicaciones_foro');
    }
};
