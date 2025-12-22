<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class RoleController extends Controller
{

    public function index()
    {
        $role = Role::all();
        return $this->actionSuccess('Role fetched',$role);
    }

        public function getPermissions()
    {
        $permissions = Permission::all();
        return $this->actionSuccess('Permissions fetched',$permissions);
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'permissions' => 'array'
        ]);

        $role = Role::create(['name' => $request->name]);

        $role->syncPermissions($request->permissions);

        return $this->actionSuccess(' Role created successfully' );
    }

    public function show($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        return $this->actionSuccess('Role show',$role);
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        $role->name = $request->name;
        $role->save();

        if ($request->permissions) {
            $role->syncPermissions($request->permissions);
        }

        return $this->actionSuccess('Role updated',$role);
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();
        return $this->actionSuccess( 'Role deleted');
    }

    
public function copy(Request $request)
{
    $request->validate([
        'role_id' => 'required|exists:roles,id',
        'name' => 'required|unique:roles,name',
    ]);

    $role = Role::with('permissions')->findOrFail($request->role_id);

    // create new role
    $newRole = Role::create([
        'name' => $request->name,
        'guard_name' => 'web',
    ]);

    // copy permissions
    $newRole->syncPermissions(
        $role->permissions->pluck('name')->toArray()
    );

    return $this->actionSuccess(
     'Role copied successfully',
        $newRole
    );
}
}