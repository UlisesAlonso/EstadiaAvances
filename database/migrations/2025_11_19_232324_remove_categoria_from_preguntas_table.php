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
        if (Schema::hasTable('preguntas') && Schema::hasColumn('preguntas', 'categoria')) {
            Schema::table('preguntas', function (Blueprint $table) {
                $table->dropColumn('categoria');
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
        if (Schema::hasTable('preguntas') && !Schema::hasColumn('preguntas', 'categoria')) {
            Schema::table('preguntas', function (Blueprint $table) {
                $table->string('categoria', 100)->nullable()->after('tipo');
            });
        }
    }
};
