<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('deputados', function (Blueprint $table) {
            $table->id();  // Cria a coluna 'id' como chave primária
            $table->string('nome');
            $table->string('nomeServidor');
            $table->string('partido');
            $table->string('endereco');
            $table->string('telefone');
            $table->string('fax')->nullable();
            $table->string('email')->nullable();
            $table->string('sitePessoal')->nullable();
            $table->string('atividadeProfissional')->nullable();
            $table->string('naturalidadeMunicipio')->nullable();
            $table->string('naturalidadeUf')->nullable();
            $table->date('dataNascimento')->nullable();
            $table->timestamps(); // Cria 'created_at' e 'updated_at'
        });
    }

    public function down()
    {
        Schema::dropIfExists('deputados');
    }
};
