<?php

namespace Chronos\Scaffolding\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Chronos\Scaffolding\Models\Permission;
use Chronos\Scaffolding\Models\Role;

class RolesController extends Controller
{
    public function index()
    {
        return view('chronos::users.roles.index');
    }

    public function permissions()
    {
        $permissions = Permission::all();
        $permissions_array = [];

        if (count($permissions) > 0) {
            foreach ($permissions as $permission) {
                $label = explode('/', $permission->label);
                $value = [];
                $array = [];
                foreach (array_reverse($label) as $k => $item) {
                    if ($k == 0) $value[$item] = ['id' => $permission->id, 'name' => $permission->name];
                    elseif ($k == 1) $array[$item] = $value;
                    else {
                        $temp = $array;
                        $array = [];
                        $array[$item] = $temp;
                    }
                }
                $permissions_array = array_merge_recursive($permissions_array, $array);
            }
        }

        return view('chronos::users.roles.permissions')->with([
            'permissions' => $permissions_array,
            'roles' => Role::where('name', '!=', 'root')->get()
        ]);
    }
}
