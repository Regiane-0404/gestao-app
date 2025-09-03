<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entidade extends Model
{
    use HasFactory;
    use SoftDeletes; // Ativa o Soft Deletes para este modelo
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nome',
        'nif',
        'nic',
        'morada',
        'codigo_postal',
        'localidade',
        'pais',
        'telefone',
        'telemovel',
        'website',
        'email',
        'consentimento_rgpd',
        'observacoes',
        'estado',
        'is_cliente',
        'is_fornecedor',
    ];
}
