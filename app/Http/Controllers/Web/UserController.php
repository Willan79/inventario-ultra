<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search', '');
        $users = User::with('roles')
            ->when($search, function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->paginate(15);

        return view('users.index', compact('users', 'search'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'string', 'in:super,admin,user'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('web.usuarios.index')
            ->with('success', 'Usuario creado exitosamente');
    }

    public function edit(int $id)
    {
        $user = User::with('roles')->findOrFail($id);
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role' => ['sometimes', 'required', 'string', 'in:super,admin,user'],
        ]);

        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('email')) {
            $user->email = $request->email;
        }
        if (!empty($request->password)) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        if ($request->has('role')) {
            $user->syncRoles([$request->role]);
        }

        return redirect()->route('web.usuarios.index')
            ->with('success', 'Usuario actualizado exitosamente');
    }

    public function destroy(int $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'No puedes eliminarte a ti mismo');
        }

        $user->delete();

        return redirect()->route('web.usuarios.index')
            ->with('success', 'Usuario eliminado exitosamente');
    }
}
