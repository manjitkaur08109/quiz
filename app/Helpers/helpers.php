<?php

use Spatie\Permission\Models\Role;

    function getRoleId($role)
    {
        return Role::where('name', $role)->value('id');
    }
 function getRoleName($id)
    {
        return Role::where('id', $id)->value('name');
    }