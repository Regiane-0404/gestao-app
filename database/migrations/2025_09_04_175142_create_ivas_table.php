<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ivas', function (Blueprint $table) {
            $table->id();
            $table->string('nome'); // Ex: "Taxa Normal", "Taxa Reduzida"
            $table->decimal('taxa', 5, 2); // Ex: 23.00, 6.00
            $table->enum('estado', ['ativo', 'inativo'])->default('ativo');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ivas');
    }
};
