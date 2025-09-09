<?php

namespace App\Http\Controllers\Gestao;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Redirect;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:gerir_gestao_acessos');
    }
    public function index()
    {
        return Inertia::render('Gestao/Users/Index', [
            // Carrega os utilizadores e o nome do seu primeiro "role"
            'users' => User::with('roles:name')->paginate(10),
        ]);
    }

    public function create()
    {
        return Inertia::render('Gestao/Users/Create', [
            'roles' => Role::all()->pluck('name'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|string|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole($request->role);

        return Redirect::route('gestao.utilizadores.index')->with('success', 'Utilizador criado com sucesso.');
    }

    public function edit(User $utilizadore) // O Laravel usa o singular da rota 'utilizadores'
    {
        return Inertia::render('Gestao/Users/Edit', [
            'user' => $utilizadore,
            'userRole' => $utilizadore->getRoleNames()->first(),
            'roles' => Role::all()->pluck('name'),
        ]);
    }

    public function update(Request $request, User $utilizadore)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:' . User::class . ',email,' . $utilizadore->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|string|exists:roles,name',
        ]);

        $utilizadore->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $utilizadore->update(['password' => Hash::make($request->password)]);
        }

        $utilizadore->syncRoles($request->role);

        return Redirect::route('gestao.utilizadores.index')->with('success', 'Utilizador atualizado com sucesso.');
    }

    public function destroy(User $utilizadore)
    {
        // Regra de negócio: Não permitir eliminar o próprio utilizador
        if ($utilizadore->id === auth()->id()) {
            return Redirect::back()->with('error', 'Não é possível eliminar o seu próprio utilizador.');
        }

        $utilizadore->delete();

        return Redirect::back()->with('success', 'Utilizador eliminado com sucesso.');
    }
}
