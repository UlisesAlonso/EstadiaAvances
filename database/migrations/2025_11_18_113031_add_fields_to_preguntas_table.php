<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('preguntas')) {
            return;
        }

        Schema::table('preguntas', function (Blueprint $table) {
            // Verificar y agregar columnas solo si no existen
            if (!Schema::hasColumn('preguntas', 'descripcion')) {
                $table->text('descripcion')->nullable();
            }
            if (!Schema::hasColumn('preguntas', 'especialidad_medica')) {
                $table->string('especialidad_medica', 100)->nullable();
            }
            if (!Schema::hasColumn('preguntas', 'fecha_asignacion')) {
                $table->date('fecha_asignacion')->nullable();
            }
            if (!Schema::hasColumn('preguntas', 'id_diagnostico')) {
                $table->unsignedBigInteger('id_diagnostico')->nullable();
            }
            if (!Schema::hasColumn('preguntas', 'id_tratamiento')) {
                $table->unsignedBigInteger('id_tratamiento')->nullable();
            }
            if (!Schema::hasColumn('preguntas', 'id_paciente')) {
                $table->unsignedBigInteger('id_paciente')->nullable();
            }
            if (!Schema::hasColumn('preguntas', 'id_medico')) {
                $table->unsignedBigInteger('id_medico')->nullable();
            }
            if (!Schema::hasColumn('preguntas', 'opciones')) {
                $table->json('opciones')->nullable();
            }
            if (!Schema::hasColumn('preguntas', 'estado')) {
                $table->string('estado', 20)->default('activa');
            }
            if (!Schema::hasColumn('preguntas', 'fecha_creacion')) {
                $table->timestamp('fecha_creacion')->nullable();
            }
        });

        // Agregar foreign keys solo si no existen
        Schema::table('preguntas', function (Blueprint $table) {
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'preguntas' 
                AND CONSTRAINT_NAME LIKE 'preguntas_%_foreign'
            ");
            $existingKeys = array_column($foreignKeys, 'CONSTRAINT_NAME');

            if (!in_array('preguntas_id_diagnostico_foreign', $existingKeys)) {
                $table->foreign('id_diagnostico')->references('id_diagnostico')->on('diagnosticos')->onDelete('set null');
            }
            if (!in_array('preguntas_id_tratamiento_foreign', $existingKeys)) {
                $table->foreign('id_tratamiento')->references('id_tratamiento')->on('tratamientos')->onDelete('set null');
            }
            if (!in_array('preguntas_id_paciente_foreign', $existingKeys)) {
                $table->foreign('id_paciente')->references('id_paciente')->on('pacientes')->onDelete('set null');
            }
            if (!in_array('preguntas_id_medico_foreign', $existingKeys)) {
                $table->foreign('id_medico')->references('id_medico')->on('medicos')->onDelete('set null');
            }
        });

        // Agregar índices solo si no existen
        Schema::table('preguntas', function (Blueprint $table) {
            $indexes = DB::select("
                SELECT INDEX_NAME 
                FROM information_schema.STATISTICS 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'preguntas'
            ");
            $existingIndexes = array_column($indexes, 'INDEX_NAME');

            if (!in_array('preguntas_id_diagnostico_index', $existingIndexes)) {
                $table->index('id_diagnostico');
            }
            if (!in_array('preguntas_id_tratamiento_index', $existingIndexes)) {
                $table->index('id_tratamiento');
            }
            if (!in_array('preguntas_id_paciente_index', $existingIndexes)) {
                $table->index('id_paciente');
            }
            if (!in_array('preguntas_id_medico_index', $existingIndexes)) {
                $table->index('id_medico');
            }
            if (!in_array('preguntas_estado_index', $existingIndexes)) {
                $table->index('estado');
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
        Schema::table('preguntas', function (Blueprint $table) {
            $table->dropForeign(['id_diagnostico']);
            $table->dropForeign(['id_tratamiento']);
            $table->dropForeign(['id_paciente']);
            $table->dropForeign(['id_medico']);
            
            $table->dropColumn([
                'descripcion',
                'especialidad_medica',
                'fecha_asignacion',
                'id_diagnostico',
                'id_tratamiento',
                'id_paciente',
                'id_medico',
                'opciones',
                'estado',
                'fecha_creacion'
            ]);
        });
    }
};
