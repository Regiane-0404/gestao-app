<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Iva extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nome',
        'taxa',
        'estado',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'taxa' => 'decimal:2',
    ];

    /**
     * Uma taxa de IVA pode estar associada a muitos Artigos.
     */
    public function artigos()
    {
        return $this->hasMany(Artigo::class);
    }
}
