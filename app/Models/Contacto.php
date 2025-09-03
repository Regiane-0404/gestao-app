<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contacto extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'entidade_id',
        'contacto_funcao_id',
        'nome',
        'apelido',
        'telefone',
        'telemovel',
        'email',
        'consentimento_rgpd',
        'observacoes',
        'estado',
    ];

    /**
     * Um Contacto pertence a uma Entidade.
     */
    public function entidade()
    {
        return $this->belongsTo(Entidade::class);
    }

    /**
     * Um Contacto pertence a uma ContactoFuncao.
     */
    public function funcao()
    {
        return $this->belongsTo(ContactoFuncao::class, 'contacto_funcao_id');
    }
}
