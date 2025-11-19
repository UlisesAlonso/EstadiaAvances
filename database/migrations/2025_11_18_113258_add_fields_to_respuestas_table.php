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
        Schema::table('respuestas', function (Blueprint $table) {
            $table->unsignedBigInteger('id_paciente')->nullable()->after('id_usuario');
            $table->timestamp('fecha_hora')->nullable()->after('fecha');
            $table->boolean('cumplimiento')->default(false)->after('fecha_hora');
            
            // Foreign key
            $table->foreign('id_paciente')->references('id_paciente')->on('pacientes')->onDelete('cascade');
            
            // Índice
            $table->index('id_paciente');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('respuestas', function (Blueprint $table) {
            $table->dropForeign(['id_paciente']);
            $table->dropColumn(['id_paciente', 'fecha_hora', 'cumplimiento']);
        });
    }
};
