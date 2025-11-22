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
        if (!Schema::hasTable('respuestas')) {
            return;
        }

        Schema::table('respuestas', function (Blueprint $table) {
            if (!Schema::hasColumn('respuestas', 'id_paciente')) {
                $table->unsignedBigInteger('id_paciente')->nullable();
            }
            if (!Schema::hasColumn('respuestas', 'fecha_hora')) {
                $table->timestamp('fecha_hora')->nullable();
            }
            if (!Schema::hasColumn('respuestas', 'cumplimiento')) {
                $table->boolean('cumplimiento')->default(false);
            }
        });

        // Agregar foreign key solo si no existe
        Schema::table('respuestas', function (Blueprint $table) {
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'respuestas' 
                AND CONSTRAINT_NAME LIKE 'respuestas_%_foreign'
            ");
            $existingKeys = array_column($foreignKeys, 'CONSTRAINT_NAME');

            if (!in_array('respuestas_id_paciente_foreign', $existingKeys) && Schema::hasColumn('respuestas', 'id_paciente')) {
                $table->foreign('id_paciente')->references('id_paciente')->on('pacientes')->onDelete('cascade');
            }
        });

        // Agregar índice solo si no existe
        Schema::table('respuestas', function (Blueprint $table) {
            $indexes = DB::select("
                SELECT INDEX_NAME 
                FROM information_schema.STATISTICS 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'respuestas'
            ");
            $existingIndexes = array_column($indexes, 'INDEX_NAME');

            if (!in_array('respuestas_id_paciente_index', $existingIndexes) && Schema::hasColumn('respuestas', 'id_paciente')) {
                $table->index('id_paciente');
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
        Schema::table('respuestas', function (Blueprint $table) {
            $table->dropForeign(['id_paciente']);
            $table->dropColumn(['id_paciente', 'fecha_hora', 'cumplimiento']);
        });
    }
};
