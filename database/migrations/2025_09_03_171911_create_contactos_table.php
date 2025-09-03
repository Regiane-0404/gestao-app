<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contactos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entidade_id')->constrained('entidades')->onDelete('cascade');
            $table->foreignId('contacto_funcao_id')->nullable()->constrained('contacto_funcoes')->onDelete('set null');
            $table->string('nome');
            $table->string('apelido')->nullable();
            $table->string('telefone', 20)->nullable();
            $table->string('telemovel', 20)->nullable();
            $table->string('email')->nullable();
            $table->boolean('consentimento_rgpd')->default(false);
            $table->text('observacoes')->nullable();
            $table->enum('estado', ['ativo', 'inativo'])->default('ativo');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contactos');
    }
};
