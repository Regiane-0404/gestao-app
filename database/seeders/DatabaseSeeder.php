<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\ContactoFuncaoSeeder;
 use Database\Seeders\PermissionSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Se quiser criar um utilizador de teste, pode descomentar estas linhas:
        // User::factory()->create([
        //     'name' => 'Admin User',
        //     'email' => 'admin@example.com',
        // ]);

        // Chama os outros seeders que criámos
        $this->call([
            ContactoFuncaoSeeder::class,
            PermissionSeeder::class,
        ]);
    }
}