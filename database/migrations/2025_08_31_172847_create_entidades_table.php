<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entidades', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('nif', 20)->unique()->nullable();
            $table->string('nic', 20)->unique()->nullable(); // <-- nic está aqui
            $table->string('morada')->nullable();
            $table->string('codigo_postal', 20)->nullable();
            $table->string('localidade')->nullable();
            $table->string('pais')->nullable();
            $table->string('telefone', 20)->nullable();
            $table->string('telemovel', 20)->nullable();
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->boolean('consentimento_rgpd')->default(false);
            $table->text('observacoes')->nullable();
            $table->enum('estado', ['ativo', 'inativo'])->default('ativo');
            $table->boolean('is_cliente')->default(false); 
            $table->boolean('is_fornecedor')->default(false); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entidades');
    }
};
