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

        $admin = User::updateOrCreate(
            [
                'email' => 'admin@admin.com'
            ],

            [
                'name' => 'Admin',
                'phone_no' => '1234567890',
                'password' => Hash::make('qwerty'),
                'role_id' => getRoleId('admin')
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
                'role_id' => getRoleId('user')
            ]
        );
        $user->assignRole('user');
    }
}
