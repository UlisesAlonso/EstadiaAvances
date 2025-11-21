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
        if (Schema::hasTable('preguntas')) {
            Schema::table('preguntas', function (Blueprint $table) {
                // Eliminar columna 'categoria' si existe (estructura antigua)
                if (Schema::hasColumn('preguntas', 'categoria')) {
                    $table->dropColumn('categoria');
                }
                
                // Renombrar 'texto' a 'descripcion' si existe y 'descripcion' no existe
                if (Schema::hasColumn('preguntas', 'texto') && !Schema::hasColumn('preguntas', 'descripcion')) {
                    DB::statement('ALTER TABLE preguntas CHANGE texto descripcion TEXT');
                }
                
                // Cambiar el enum de tipo si es necesario
                if (Schema::hasColumn('preguntas', 'tipo')) {
                    // Actualizar valores 'cerrada' a 'opcion_multiple'
                    DB::statement("UPDATE preguntas SET tipo = 'opcion_multiple' WHERE tipo = 'cerrada'");
                    // Modificar el enum
                    DB::statement("ALTER TABLE preguntas MODIFY tipo ENUM('abierta', 'opcion_multiple')");
                }
                
                // Agregar nuevas columnas si no existen
                if (!Schema::hasColumn('preguntas', 'opciones_multiple')) {
                    $table->text('opciones_multiple')->nullable()->after('tipo');
                }
                
                if (!Schema::hasColumn('preguntas', 'especialidad_medica')) {
                    $table->string('especialidad_medica', 100)->nullable()->after('opciones_multiple');
                }
                
                if (!Schema::hasColumn('preguntas', 'fecha_asignacion')) {
                    $table->date('fecha_asignacion')->nullable()->after('especialidad_medica');
                }
                
                if (!Schema::hasColumn('preguntas', 'id_diagnostico')) {
                    $table->unsignedBigInteger('id_diagnostico')->nullable()->after('fecha_asignacion');
                }
                
                if (!Schema::hasColumn('preguntas', 'id_tratamiento')) {
                    $table->unsignedBigInteger('id_tratamiento')->nullable()->after('id_diagnostico');
                }
                
                if (!Schema::hasColumn('preguntas', 'id_paciente')) {
                    $table->unsignedBigInteger('id_paciente')->nullable()->after('id_tratamiento');
                }
                
                if (!Schema::hasColumn('preguntas', 'id_medico')) {
                    // Primero agregar como nullable, luego actualizar con valores por defecto
                    $table->unsignedBigInteger('id_medico')->nullable()->after('id_paciente');
                }
                
                // Actualizar registros existentes sin id_medico
                if (Schema::hasColumn('preguntas', 'id_medico')) {
                    // Obtener el primer médico disponible y asignarlo a preguntas sin médico
                    $primerMedico = DB::table('medicos')->first();
                    if ($primerMedico) {
                        DB::table('preguntas')
                            ->whereNull('id_medico')
                            ->update(['id_medico' => $primerMedico->id_medico]);
                    }
                    
                    // Cambiar la columna a NOT NULL después de actualizar los datos
                    DB::statement('ALTER TABLE preguntas MODIFY id_medico BIGINT UNSIGNED NOT NULL');
                }
                
                if (!Schema::hasColumn('preguntas', 'fecha_creacion')) {
                    $table->timestamp('fecha_creacion')->nullable()->useCurrent()->after('id_medico');
                }
                
                if (!Schema::hasColumn('preguntas', 'activa')) {
                    $table->boolean('activa')->default(true)->after('fecha_creacion');
                }
            });
            
            // Agregar foreign keys
            Schema::table('preguntas', function (Blueprint $table) {
                try {
                    if (Schema::hasColumn('preguntas', 'id_diagnostico')) {
                        $table->foreign('id_diagnostico')->references('id_diagnostico')->on('diagnosticos')->onDelete('set null');
                    }
                } catch (\Exception $e) {
                    // Foreign key ya existe
                }
                
                try {
                    if (Schema::hasColumn('preguntas', 'id_tratamiento')) {
                        $table->foreign('id_tratamiento')->references('id_tratamiento')->on('tratamientos')->onDelete('set null');
                    }
                } catch (\Exception $e) {
                    // Foreign key ya existe
                }
                
                try {
                    if (Schema::hasColumn('preguntas', 'id_paciente')) {
                        $table->foreign('id_paciente')->references('id_paciente')->on('pacientes')->onDelete('cascade');
                    }
                } catch (\Exception $e) {
                    // Foreign key ya existe
                }
                
                try {
                    if (Schema::hasColumn('preguntas', 'id_medico')) {
                        $table->foreign('id_medico')->references('id_medico')->on('medicos')->onDelete('cascade');
                    }
                } catch (\Exception $e) {
                    // Foreign key ya existe
                }
            });
            
            // Agregar índices
            Schema::table('preguntas', function (Blueprint $table) {
                if (Schema::hasColumn('preguntas', 'id_paciente')) {
                    try {
                        $table->index('id_paciente');
                    } catch (\Exception $e) {
                        // Índice ya existe
                    }
                }
                
                if (Schema::hasColumn('preguntas', 'id_medico')) {
                    try {
                        $table->index('id_medico');
                    } catch (\Exception $e) {
                        // Índice ya existe
                    }
                }
                
                if (Schema::hasColumn('preguntas', 'fecha_asignacion')) {
                    try {
                        $table->index('fecha_asignacion');
                    } catch (\Exception $e) {
                        // Índice ya existe
                    }
                }
                
                if (Schema::hasColumn('preguntas', 'activa')) {
                    try {
                        $table->index('activa');
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
        if (Schema::hasTable('preguntas')) {
            Schema::table('preguntas', function (Blueprint $table) {
                // Eliminar foreign keys
                try {
                    $table->dropForeign(['id_diagnostico']);
                } catch (\Exception $e) {}
                
                try {
                    $table->dropForeign(['id_tratamiento']);
                } catch (\Exception $e) {}
                
                try {
                    $table->dropForeign(['id_paciente']);
                } catch (\Exception $e) {}
                
                try {
                    $table->dropForeign(['id_medico']);
                } catch (\Exception $e) {}
                
                // Eliminar columnas agregadas
                $columnsToDrop = [
                    'opciones_multiple',
                    'especialidad_medica',
                    'fecha_asignacion',
                    'id_diagnostico',
                    'id_tratamiento',
                    'id_paciente',
                    'id_medico',
                    'fecha_creacion',
                    'activa'
                ];
                
                foreach ($columnsToDrop as $column) {
                    if (Schema::hasColumn('preguntas', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
