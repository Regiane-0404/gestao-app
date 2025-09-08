<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ContactoFuncao;

class ContactoFuncaoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $funcoes = [
            ['nome' => 'Gerente'],
            ['nome' => 'Financeiro'],
            ['nome' => 'Técnico'],
            ['nome' => 'Comercial'],
            ['nome' => 'CEO'],
            ['nome' => 'Administrativo'],
        ];

        foreach ($funcoes as $funcao) {
            ContactoFuncao::firstOrCreate($funcao);
        }
    }
}
