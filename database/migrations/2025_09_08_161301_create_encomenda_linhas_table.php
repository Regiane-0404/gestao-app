<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('encomenda_linhas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('encomenda_id')->constrained('encomendas')->onDelete('cascade');
            $table->foreignId('artigo_id')->nullable()->constrained('artigos')->onDelete('set null');

            // Dados copiados (espelho das linhas da proposta)
            $table->string('referencia');
            $table->text('descricao');
            $table->decimal('quantidade', 10, 2);
            $table->decimal('preco_unitario', 10, 2);
            $table->decimal('taxa_iva', 5, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encomenda_linhas');
    }
};
