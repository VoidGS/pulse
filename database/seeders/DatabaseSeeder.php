<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder {
    /**
     * Seed the application's database.
     */
    public function run(): void {
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

        Permission::create(['name' => 'see schedules'])->syncRoles([$admin, $member]);
        Permission::create(['name' => 'create schedules'])->syncRoles([$admin]);
        Permission::create(['name' => 'edit schedules'])->syncRoles([$admin]);
        Permission::create(['name' => 'delete schedules'])->syncRoles([$admin]);

        $voidgs = User::factory()->create([
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

        Customer::factory(10)->create();

        $team = Team::factory()->create(['user_id' => $voidgs->id]);
        $voidgs->switchTeam($team);

        Service::factory()->create([
            'name' => 'Sessão de psico',
            'price' => 150.00,
            'team_id' => $team->id
        ]);
    }
}
