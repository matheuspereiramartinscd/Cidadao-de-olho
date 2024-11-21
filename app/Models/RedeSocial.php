<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RedeSocial extends Model
{
    protected $table = 'rede_sociais'; // Defina o nome correto da tabela

    protected $fillable = ['deputado_id', 'nome', 'url'];
}
