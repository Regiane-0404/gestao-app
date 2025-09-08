<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EncomendaLinha extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'encomenda_linhas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'encomenda_id',
        'artigo_id',
        'referencia',
        'descricao',
        'quantidade',
        'preco_unitario',
        'taxa_iva',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'quantidade' => 'decimal:2',
        'preco_unitario' => 'decimal:2',
        'taxa_iva' => 'decimal:2',
    ];

    /**
     * Uma Linha pertence a uma Encomenda.
     */
    public function encomenda()
    {
        return $this->belongsTo(Encomenda::class);
    }

    /**
     * Uma Linha pode estar ligada a um Artigo do catálogo.
     */
    public function artigo()
    {
        return $this->belongsTo(Artigo::class);
    }
}
