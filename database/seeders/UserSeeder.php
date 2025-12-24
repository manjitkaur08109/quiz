<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    public function run(): void
    {
      $adminRole = Role::where('name', 'admin')->first();

      $userRole = Role::where('name', 'user')->first();

        $admin = User::updateOrCreate(
            [
                'email' => 'admin@admin.com'
            ],

            [
                'name' => 'Admin',
                'phone_no' => '1234567890',
                'password' => Hash::make('qwerty'),
                'account_type' => 'admin',
                'role_id' => $adminRole->id
            ]
        );
        $admin->assignRole('admin');

        $user = User::updateOrCreate(
            [
                'email' => 'abc@abc.com'
            ],

            [
                'name' => 'ABC',
                'phone_no' => '1234567890',
                'password' => Hash::make(123456),
                'account_type' => 'user',
                'role_id' => $userRole->id
            ]
        );
        $user->assignRole('user');
    }
}
