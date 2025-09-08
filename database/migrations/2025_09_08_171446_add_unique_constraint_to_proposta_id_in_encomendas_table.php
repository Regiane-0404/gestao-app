<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('encomendas', function (Blueprint $table) {
            // Permite múltiplos valores nulos, mas cada proposta_id só pode aparecer uma vez.
            $table->unique(['proposta_id'])->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('encomendas', function (Blueprint $table) {
            $table->dropUnique(['proposta_id']);
        });
    }
};
