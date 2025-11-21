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
        // Verificar si la columna ya tiene AUTO_INCREMENT
        $columnInfo = DB::select("SHOW COLUMNS FROM analisis_clinicos WHERE Field = 'id_analisis'");
        
        if (!empty($columnInfo) && strpos($columnInfo[0]->Extra, 'auto_increment') === false) {
            // Modificar la columna para que sea AUTO_INCREMENT y PRIMARY KEY
            DB::statement("ALTER TABLE analisis_clinicos MODIFY COLUMN id_analisis INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No revertir el cambio ya que es necesario para el funcionamiento
    }
};
