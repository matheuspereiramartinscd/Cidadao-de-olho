<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
 * Modelo responsável por representar as redes sociais de um deputado.
 * Define os atributos e o relacionamento com o deputado.
 */
class RedeSocial extends Model
{
    protected $table = 'rede_sociais'; // Defina o nome correto da tabela

    protected $fillable = ['deputado_id', 'nome', 'url'];
}
