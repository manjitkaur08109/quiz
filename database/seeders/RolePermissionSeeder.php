<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Permissions
        $permissions = [
           
            'create user',
            'edit user',
            'delete user',
            'view user',
            'create category',
            'edit category',
            'delete category',
            'view category',
             'create quiz',
            'edit quiz',
            'delete quiz',
            'view quiz',
            'create role',
            'edit role',
            'delete role',
            'view role',
            'view dashboard',
            'view payments',
            'view discover',
            'view myLearning',
            ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $user  = Role::firstOrCreate(['name' => 'user']);

        // Assign permissions
        $admin->givePermissionTo($permissions);

        $user->givePermissionTo(['view quiz','view category','view myLearning','view discover']);
    }
    }

