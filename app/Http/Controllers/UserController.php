<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\PasswordValidationRules;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Jetstream\Jetstream;

class UserController extends Controller {
    use PasswordValidationRules;

    public function index() {
        return inertia('Users/Index', [
            'users' => UserResource::collection(User::with(['teams', 'ownedTeams'])->get()),
        ]);
    }

    public function create() {
        $this->authorize('create', User::class);

        return inertia('Auth/Register');
    }

    public function store(Request $request) {
        $this->authorize('create', User::class);

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'terms'    => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return to_route('users.index');
    }
}
