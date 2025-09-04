<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artigos', function (Blueprint $table) {
            $table->id();
            $table->string('referencia')->unique();
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->decimal('preco', 10, 2); // Preço sem IVA

            // Chave estrangeira para a tabela de IVA
            $table->foreignId('iva_id')->nullable()->constrained('ivas')->onDelete('set null');

            $table->string('foto_path')->nullable(); // Caminho para a imagem
            $table->text('observacoes')->nullable();
            $table->enum('estado', ['ativo', 'inativo'])->default('ativo');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artigos');
    }
};
