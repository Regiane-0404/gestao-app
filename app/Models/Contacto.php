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
    
    // --- INÍCIO DA ALTERAÇÃO ---
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // Define uma ação a ser executada ANTES de um registo ser eliminado (soft deleted)
        static::deleting(function ($contacto) {
            // Garante que, se o contacto tiver relações que o impeçam de ser
            // eliminado permanentemente, o estado é atualizado mesmo assim.
            if ($contacto->isForceDeleting()) {
                return; // Não faz nada se for um "force delete"
            }
            
            // Altera o estado para "inativo"
            $contacto->estado = 'inativo';
            $contacto->save(); // Salva a alteração do estado
        });
    }
    // --- FIM DA ALTERAÇÃO ---
}