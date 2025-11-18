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
        Schema::create('tokens_recuperacion', function (Blueprint $table) {
            $table->id('id_token');
            $table->unsignedBigInteger('id_usuario');
            $table->string('token', 255);
            $table->dateTime('expiracion');
            $table->boolean('usado')->default(false);
            
            $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->onDelete('cascade');
            $table->index('id_usuario');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tokens_recuperacion');
    }
};
