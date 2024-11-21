<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRedeSociaisTable extends Migration
{
    public function up()
    {
        Schema::create('rede_sociais', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deputado_id');
            $table->string('nome');
            $table->string('url');
            $table->timestamps();
    
            // Adicionando uma chave estrangeira
            $table->foreign('deputado_id')->references('id')->on('deputados')->onDelete('cascade');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('rede_sociais');
    }
}
