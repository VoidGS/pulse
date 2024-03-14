<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $admin = Role::create(['name' => 'admin']);
        $member = Role::create(['name' => 'member']);

        Permission::create(['name' => 'see users'])->syncRoles([$admin, $member]);
        Permission::create(['name' => 'create users'])->syncRoles([$admin]);
        Permission::create(['name' => 'edit users'])->syncRoles([$admin]);
        Permission::create(['name' => 'delete users'])->syncRoles([$admin]);

        Permission::create(['name' => 'see services'])->syncRoles([$admin, $member]);
        Permission::create(['name' => 'create services'])->syncRoles([$admin]);
        Permission::create(['name' => 'edit services'])->syncRoles([$admin]);
        Permission::create(['name' => 'delete services'])->syncRoles([$admin]);

        Permission::create(['name' => 'see customers'])->syncRoles([$admin, $member]);
        Permission::create(['name' => 'create customers'])->syncRoles([$admin]);
        Permission::create(['name' => 'edit customers'])->syncRoles([$admin]);
        Permission::create(['name' => 'delete customers'])->syncRoles([$admin]);

        User::factory()->create([
            'name' => 'voidgs',
            'email' => 'void@test.com',
        ])->assignRole($admin);

        User::factory()->create([
            'name' => 'crift',
            'email' => 'crift@test.com',
        ])->assignRole($member);

        User::factory(20)->create()->each(function ($user) {
            $user->assignRole('member');
        });

        Customer::factory(20)->create();
    }
}
