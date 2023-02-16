<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        Role::create(['name' => 'super-admin']);
        Role::create(['name' => 'admin']);
    }
}
