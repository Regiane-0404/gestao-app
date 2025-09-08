<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Encomenda extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'entidade_id',
        'proposta_id',
        'data_encomenda',
        'valor_total',
        'estado',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'data_encomenda' => 'date',
        'valor_total' => 'decimal:2',
    ];

    /**
     * Uma Encomenda pertence a uma Entidade (Cliente).
     */
    public function cliente()
    {
        return $this->belongsTo(Entidade::class, 'entidade_id');
    }

    /**
     * Uma Encomenda tem muitas Linhas.
     */
    public function linhas()
    {
        return $this->hasMany(EncomendaLinha::class);
    }

    /**
     * Uma Encomenda pode ter vindo de uma Proposta.
     */
    public function proposta()
    {
        return $this->belongsTo(Proposta::class);
    }
}
