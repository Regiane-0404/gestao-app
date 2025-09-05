<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposta_linhas', function (Blueprint $table) {
            // Linhas da Proposta
            $table->id();
            $table->foreignId('proposta_id')->constrained('propostas')->onDelete('cascade');
            $table->foreignId('artigo_id')->nullable()->constrained('artigos')->onDelete('set null');

            // Dados copiados do artigo no momento da criação da linha (para manter o histórico)
            $table->string('referencia');
            $table->text('descricao');
            $table->decimal('quantidade', 10, 2)->default(1.00);
            $table->decimal('preco_unitario', 10, 2);
            $table->decimal('taxa_iva', 5, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposta_linhas');
    }
};
