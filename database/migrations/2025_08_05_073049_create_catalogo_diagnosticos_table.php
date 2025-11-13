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
        Schema::create('catalogo_diagnosticos', function (Blueprint $table) {
            $table->id('id_diagnostico');
            $table->string('codigo', 50)->nullable()->unique();
            $table->text('descripcion_clinica');
            $table->string('categoria_medica', 100);
            $table->unsignedBigInteger('id_usuario_creador');
            $table->unsignedBigInteger('id_usuario_modificador')->nullable();
            $table->dateTime('fecha_creacion')->useCurrent();
            $table->dateTime('fecha_modificacion')->nullable();
            
            $table->foreign('id_usuario_creador')->references('id_usuario')->on('usuarios')->onDelete('restrict');
            $table->foreign('id_usuario_modificador')->references('id_usuario')->on('usuarios')->onDelete('set null');
            $table->index('codigo');
            $table->index('categoria_medica');
            $table->index('fecha_creacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalogo_diagnosticos');
    }
};
