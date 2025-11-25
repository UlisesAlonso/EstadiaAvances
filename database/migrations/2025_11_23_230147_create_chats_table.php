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
        Schema::create('chats', function (Blueprint $table) {
            $table->id('id_chat');
            $table->unsignedBigInteger('user_one_id');
            $table->unsignedBigInteger('user_two_id');
            $table->timestamps();
            
            // Índices antes de las foreign keys
            $table->index('user_one_id');
            $table->index('user_two_id');
            
            // Relaciones con la tabla de usuarios
            $table->foreign('user_one_id')
                  ->references('id_usuario')
                  ->on('usuarios')
                  ->onDelete('cascade');
                  
            $table->foreign('user_two_id')
                  ->references('id_usuario')
                  ->on('usuarios')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chats');
    }
};
