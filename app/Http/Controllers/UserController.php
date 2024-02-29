<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\PasswordValidationRules;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Jetstream\Jetstream;
use Spatie\Permission\Models\Role;

class UserController extends Controller {
    use PasswordValidationRules;

    public function __construct() {
        $this->authorizeResource(User::class);
    }

    public function index() {
        return inertia('Users/Index', [
            'users' => UserResource::collection(User::with(['teams', 'ownedTeams'])->where('active', true)->get()),
        ]);
    }

    public function create() {
        return inertia('Users/Create', [
            'roles' => Role::all(),
        ]);
    }

    public function store(Request $request) {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'role' => ['required', 'string', 'min:1'],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ])->assignRole($request->role);

        return to_route('users.index')->toastSuccess('Usuário cadastrado com sucesso!');
    }

    public function edit(User $user) {
        $user->load(['teams', 'ownedTeams']);

        return inertia('Users/Edit', [
            'user' => UserResource::make($user),
            'roles' => Role::all(),
        ]);
    }

    public function update(Request $request, User $user) {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'role' => ['required', 'string', 'min:1'],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email
        ]);

        if (!$user->hasAnyRole($request->role)) {
            $user->syncRoles($request->role);
        }

        return to_route('users.index')->toastSuccess('Usuário atualizado com sucesso!');
    }

    public function destroy(User $user) {
        $user->active = false;
        $user->save();

        return to_route('users.index')->toastSuccess('Usuário inativado com sucesso!');
    }
}
