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
        Schema::table('analisis_clinicos', function (Blueprint $table) {
            // Verificar si las columnas ya existen antes de agregarlas
            if (!Schema::hasColumn('analisis_clinicos', 'descripcion')) {
                $table->text('descripcion')->nullable()->after('tipo_analisis');
            }
            if (!Schema::hasColumn('analisis_clinicos', 'valores_cuantitativos')) {
                $table->json('valores_cuantitativos')->nullable()->after('resultado');
            }
            if (!Schema::hasColumn('analisis_clinicos', 'observaciones_clinicas')) {
                $table->text('observaciones_clinicas')->nullable()->after('valores_cuantitativos');
            }
            if (!Schema::hasColumn('analisis_clinicos', 'estado')) {
                $table->string('estado', 20)->default('activo')->after('fecha');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('analisis_clinicos', function (Blueprint $table) {
            if (Schema::hasColumn('analisis_clinicos', 'descripcion')) {
                $table->dropColumn('descripcion');
            }
            if (Schema::hasColumn('analisis_clinicos', 'valores_cuantitativos')) {
                $table->dropColumn('valores_cuantitativos');
            }
            if (Schema::hasColumn('analisis_clinicos', 'observaciones_clinicas')) {
                $table->dropColumn('observaciones_clinicas');
            }
            if (Schema::hasColumn('analisis_clinicos', 'estado')) {
                $table->dropColumn('estado');
            }
        });
    }
};
