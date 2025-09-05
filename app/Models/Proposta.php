<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proposta extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'entidade_id',
        'data_proposta',
        'validade',
        'valor_total',
        'estado',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'data_proposta' => 'date',
        'validade' => 'date',
        'valor_total' => 'decimal:2',
    ];

    /**
     * Uma Proposta pertence a uma Entidade (Cliente).
     */
    public function cliente()
    {
        return $this->belongsTo(Entidade::class, 'entidade_id');
    }

    /**
     * Uma Proposta tem muitas Linhas.
     */
    public function linhas()
    {
        return $this->hasMany(PropostaLinha::class);
    }
}
