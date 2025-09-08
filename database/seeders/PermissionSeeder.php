<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpa a cache de permissões
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Lista de permissões a serem criadas, agrupadas por módulo
        $permissions = [
            'entidades' => ['ver', 'criar', 'editar', 'eliminar'],
            'contactos' => ['ver', 'criar', 'editar', 'eliminar'],
            'artigos' => ['ver', 'criar', 'editar', 'eliminar'],
            'propostas' => ['ver', 'criar', 'editar', 'eliminar', 'fechar', 'converter'],
            'encomendas' => ['ver', 'criar', 'editar', 'eliminar'],
            'gestao_acessos' => ['ver', 'gerir'],
            // Adicionar outros módulos aqui no futuro
        ];

        // Cria as permissões
        foreach ($permissions as $module => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "{$action}_{$module}"]);
            }
        }

        // Cria o Role "Administrador" e atribui-lhe TODAS as permissões
        $adminRole = Role::firstOrCreate(['name' => 'Administrador']);
        $adminRole->givePermissionTo(Permission::all());

        // Cria um Role "Comercial" de exemplo e atribui-lhe algumas permissões
        $comercialRole = Role::firstOrCreate(['name' => 'Comercial']);
        $comercialRole->givePermissionTo([
            'ver_entidades', 'criar_entidades', 'editar_entidades',
            'ver_contactos', 'criar_contactos', 'editar_contactos',
            'ver_artigos',
            'ver_propostas', 'criar_propostas', 'editar_propostas', 'fechar_propostas', 'converter_propostas',
        ]);
    }
}