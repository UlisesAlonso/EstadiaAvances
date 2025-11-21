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
        Schema::table('historial_clinico', function (Blueprint $table) {
            // Campos de antecedentes médicos (solo se llenan al crear el primer historial)
            $table->text('alergias')->nullable()->after('resultados_analisis');
            $table->text('enfermedades_familiares')->nullable()->after('alergias');
            $table->text('cirugias_previas')->nullable()->after('enfermedades_familiares');
            $table->enum('consumo_tabaco', ['si', 'no', 'ex_fumador'])->nullable()->after('cirugias_previas');
            $table->enum('consumo_alcohol', ['si', 'no', 'ocasional'])->nullable()->after('consumo_tabaco');
            $table->enum('realiza_ejercicio', ['si', 'no', 'ocasional'])->nullable()->after('consumo_alcohol');
            $table->text('tipo_alimentacion')->nullable()->after('realiza_ejercicio');
            $table->text('observaciones_antecedentes')->nullable()->after('tipo_alimentacion');
            
            // Relación con análisis
            $table->unsignedBigInteger('id_analisis')->nullable()->after('id_tratamiento');
            $table->foreign('id_analisis')->references('id_analisis')->on('analisis')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('historial_clinico', function (Blueprint $table) {
            $table->dropForeign(['id_analisis']);
            $table->dropColumn([
                'alergias',
                'enfermedades_familiares',
                'cirugias_previas',
                'consumo_tabaco',
                'consumo_alcohol',
                'realiza_ejercicio',
                'tipo_alimentacion',
                'observaciones_antecedentes',
                'id_analisis'
            ]);
        });
    }
};
