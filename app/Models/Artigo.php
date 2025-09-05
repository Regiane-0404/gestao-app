<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Artigo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'referencia',
        'nome',
        'descricao',
        'preco',
        'iva_id',
        'foto_path',
        'observacoes',
        'estado',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'preco' => 'decimal:2', // <-- CORRIGIDO AQUI
    ];

    /**
     * Um Artigo pertence a uma taxa de IVA.
     */
    public function iva()
    {
        return $this->belongsTo(Iva::class);
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // Gera a referência automática ao criar um novo artigo
        static::creating(function ($artigo) {
            if (empty($artigo->referencia)) {
                $artigo->referencia = self::generateReference();
            }
        });

        // Altera o estado para 'inativo' antes de fazer o soft delete
        static::deleting(function ($artigo) {
            if ($artigo->isForceDeleting()) {
                return;
            }

            $artigo->estado = 'inativo';
            $artigo->save();
        });
    }

    /**
     * Gera uma nova referência única para o artigo.
     * Exemplo: ART-00001
     */
    public static function generateReference()
    {
        $lastArtigo = self::withTrashed()->orderBy('id', 'desc')->first();
        $nextId = $lastArtigo ? $lastArtigo->id + 1 : 1;
        $paddedId = str_pad($nextId, 5, '0', STR_PAD_LEFT);

        return 'ART-' . $paddedId;
    }
}
