<?php

namespace App\Http\Controllers\Gestao;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission; // <-- Adicionar import

class RoleController extends Controller
{
    public function index()
    {
        return Inertia::render('Gestao/Roles/Index', [
            'roles' => Role::withCount('users')->paginate(10),
        ]);
    }

    public function create()
    {
        return Inertia::render('Gestao/Roles/Create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        Role::create(['name' => $validatedData['name']]);

        return Redirect::route('gestao.roles.index')->with('success', 'Grupo de permissões criado com sucesso.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    // --- INÍCIO DA ALTERAÇÃO ---
    public function edit(Role $role)
    {
        return Inertia::render('Gestao/Roles/Edit', [
            'role' => $role,
            // 1. Envia todas as permissões que existem na base de dados
            'permissions' => Permission::all()->pluck('name'),
            // 2. Envia apenas as permissões que este Role já tem
            'rolePermissions' => $role->permissions->pluck('name'),
        ]);
    }
    // --- FIM DA ALTERAÇÃO ---

    /**
     * Update the specified resource in storage.
     */
    // --- INÍCIO DA ALTERAÇÃO ---
    public function update(Request $request, Role $role)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array', // Garante que as permissões vêm como um array
        ]);

        // Atualiza o nome do Role
        $role->name = $validatedData['name'];
        $role->save();

        // Sincroniza as permissões. O Spatie trata de adicionar/remover o que for necessário.
        $role->syncPermissions($validatedData['permissions']);

        return Redirect::route('gestao.roles.index')->with('success', 'Grupo de permissões atualizado com sucesso.');
    }
    public function destroy(Role $role)
    {
        // Regra de negócio: Não permitir eliminar o role "Administrador"
        if ($role->name === 'Administrador') {
            return Redirect::back()->with('error', 'Não é possível eliminar o grupo de Administradores.');
        }

        $role->delete();

        return Redirect::back()->with('success', 'Grupo de permissões eliminado com sucesso.');
    }
}
