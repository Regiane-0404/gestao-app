<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('encomendas', function (Blueprint $table) {
            $table->id(); // Número da Encomenda
            $table->foreignId('entidade_id')->constrained('entidades'); // Cliente
            $table->foreignId('proposta_id')->nullable()->constrained('propostas'); // Ligação à proposta de origem

            $table->date('data_encomenda')->nullable(); // Data em que fica "Fechado"
            $table->decimal('valor_total', 10, 2)->default(0.00);
            $table->enum('estado', ['rascunho', 'fechado'])->default('rascunho');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encomendas');
    }
};
