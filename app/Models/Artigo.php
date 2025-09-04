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

    protected $casts = [
        'preco' => 'decimal:2',
    ];

    public function iva()
    {
        return $this->belongsTo(Iva::class);
    }

    // --- INÍCIO DA ALTERAÇÃO ---
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // Define uma ação a ser executada ANTES de um novo artigo ser criado
        static::creating(function ($artigo) {
            // Garante que só geramos a referência se ela não for fornecida manualmente
            if (empty($artigo->referencia)) {
                $artigo->referencia = self::generateReference();
            }
        });
    }

    /**
     * Gera uma nova referência única para o artigo.
     * Exemplo: ART-00001
     */
    public static function generateReference()
    {
        // Encontra o artigo com o ID mais alto (o último criado)
        $lastArtigo = self::withTrashed()->orderBy('id', 'desc')->first();

        $nextId = $lastArtigo ? $lastArtigo->id + 1 : 1;

        // Formata o número com zeros à esquerda (ex: 1 -> 00001)
        $paddedId = str_pad($nextId, 5, '0', STR_PAD_LEFT);

        return 'ART-' . $paddedId;
    }
    // --- FIM DA ALTERAÇÃO ---
}
