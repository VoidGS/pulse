<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PermissionsHelper {
    public static function getAllPermissionsArray(Request $request): array {
        if (!$request->user()) return [];

        $user = $request->user();
        $permissions = $user->getAllPermissions()->pluck('name');
        $permissionsArray = [];

        foreach ($permissions as $permission) {
            $permissionsArray[Str::snake($permission)] = $user->hasPermissionTo($permission);
        }

        return $permissionsArray;
    }
}