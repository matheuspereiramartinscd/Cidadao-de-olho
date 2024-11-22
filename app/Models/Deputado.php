<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Modelo responsável por representar os deputados no sistema.
 * Define os atributos e relacionamentos associados aos deputados.
 */
class Deputado extends Model
{
    use HasFactory;

    protected $fillable = [
        'id', 'nome', 'nomeServidor', 'partido', 'endereco', 'telefone', 'fax', 'email', 'sitePessoal', 'atividadeProfissional', 
        'naturalidadeMunicipio', 'naturalidadeUf', 'dataNascimento'
    ];

    // Definindo relacionamento com as redes sociais (se você for usar isso)
    public function redesSociais()
    {
        return $this->hasMany(RedeSocial::class);
    }

    

public function reembolsos()
{
    return $this->hasMany(Reembolso::class);
}
}
