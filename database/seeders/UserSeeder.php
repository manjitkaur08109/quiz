<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder {
    /**
    * Run the database seeds.
    */

    public function run(): void {
        
        User::updateOrCreate( [
            'email' => 'admin@admin.com'],
        
        [
            'name' => 'Admin',
                'phone_no' => '1234567890',
                'password' => Hash::make( 'qwerty' ),
                'account_type' => 'admin'
            ] ) ;
            User::factory()->count(50)->create();
    }
}
