<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $super_admin = User::create([
            'firstname' => 'Super',
            'lastname' => 'Admin',
            'email' => 'super@admin.com',
            'password' => Hash::make('12345678'),
        ]);

        $super_admin->assignRole('super-admin');

        $admin = User::create([
            'firstname' => 'System',
            'lastname' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('12345678'),
        ]);

        $admin->assignRole('admin');
    }
}
