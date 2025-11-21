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
        if (Schema::hasTable('respuestas')) {
            Schema::table('respuestas', function (Blueprint $table) {
                // Renombrar 'fecha' a 'fecha_respuesta' si existe y 'fecha_respuesta' no existe
                if (Schema::hasColumn('respuestas', 'fecha') && !Schema::hasColumn('respuestas', 'fecha_respuesta')) {
                    Schema::getConnection()->statement('ALTER TABLE respuestas CHANGE fecha fecha_respuesta DATETIME DEFAULT CURRENT_TIMESTAMP');
                }
                
                // Agregar fecha_respuesta si no existe
                if (!Schema::hasColumn('respuestas', 'fecha_respuesta')) {
                    $table->dateTime('fecha_respuesta')->useCurrent()->after('respuesta');
                }
                
                // Agregar cumplimiento si no existe
                if (!Schema::hasColumn('respuestas', 'cumplimiento')) {
                    $table->boolean('cumplimiento')->default(false)->after('fecha_respuesta');
                }
            });
            
            // Agregar índices
            Schema::table('respuestas', function (Blueprint $table) {
                if (Schema::hasColumn('respuestas', 'fecha_respuesta')) {
                    try {
                        $table->index('fecha_respuesta');
                    } catch (\Exception $e) {
                        // Índice ya existe
                    }
                }
                
                if (Schema::hasColumn('respuestas', 'cumplimiento')) {
                    try {
                        $table->index('cumplimiento');
                    } catch (\Exception $e) {
                        // Índice ya existe
                    }
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('respuestas')) {
            Schema::table('respuestas', function (Blueprint $table) {
                if (Schema::hasColumn('respuestas', 'cumplimiento')) {
                    $table->dropColumn('cumplimiento');
                }
                
                if (Schema::hasColumn('respuestas', 'fecha_respuesta') && !Schema::hasColumn('respuestas', 'fecha')) {
                    Schema::getConnection()->statement('ALTER TABLE respuestas CHANGE fecha_respuesta fecha DATE');
                }
            });
        }
    }
};
