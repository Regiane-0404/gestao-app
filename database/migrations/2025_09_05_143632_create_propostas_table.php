<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('propostas', function (Blueprint $table) {
            // Cabeçalho da Proposta
            $table->id(); // Será o nosso "Número"
            $table->foreignId('entidade_id')->constrained('entidades'); // O Cliente
            $table->date('data_proposta')->nullable(); // Data em que fica "Fechado"
            $table->date('validade');
            $table->decimal('valor_total', 10, 2)->default(0.00); // Calculado a partir das linhas
            $table->enum('estado', ['rascunho', 'fechado'])->default('rascunho');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('propostas');
    }
};
