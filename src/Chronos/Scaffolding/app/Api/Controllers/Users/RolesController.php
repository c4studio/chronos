<?php

namespace Chronos\Scaffolding\Api\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Chronos\Scaffolding\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rule;


class RolesController extends Controller
{
    public function index(Request $request)
    {
        $itemsPerPage = $request->has('perPage')
            ? $request->get('perPage') == 0 ? Role::query()->count() : $request->get('perPage')
            : Config::get('chronos.items_per_page');

        $q = Role::uncloaked();

        // filter
        if ($request->has('filters')) {
            $filters = $request->get('filters');

            if (isset($filters['search']) && $filters['search'] != '')
                $q->where('name', 'like', '%' . $filters['search'] . '%');
        }

        // sort
        if ($request->has('sortBy') && $request->get('sortBy') != '') {
            $sortBy = $request->get('sortBy');
            $sortOrder = $request->get('sortOrder');

            switch ($sortBy) {
                case 'name':
                    $q->orderBy($sortBy, (isset($sortOrder) && $sortOrder === 'true') ? 'ASC' : 'DESC');
                    break;
                default:
                    return response()->json([
                        'alerts' => [
                            (object) [
                                'type' => 'error',
                                'title' => trans('chronos.scaffolding::alerts.Error.'),
                                'message' => trans('chronos.scaffolding::alerts.Invalid sortBy argument: :arg.', ['arg' => $sortBy]),
                            ]
                        ],
                        'status' => 200
                    ], 200);
            }
        }
        else
            $q->orderBy('name', 'ASC');

        // pagination
        $data = $q->paginate($itemsPerPage);


        return response()->json($data, 200);
    }

    public function destroy(Role $role)
    {
        if ($role->delete())
            return response()->json([
                'alerts' => [
                    (object) [
                        'type' => 'success',
                        'title' => trans('chronos.scaffolding::alerts.Success.'),
                        'message' => trans('chronos.scaffolding::alerts.Role successfully deleted.'),
                    ]
                ],
                'status' => 200
            ], 200);
        else
            return response()->json([
                'alerts' => [
                    (object) [
                        'type' => 'error',
                        'title' => trans('chronos.scaffolding::alerts.Error.'),
                        'message' => trans('chronos.scaffolding::alerts.Role deletion was unsuccessful.'),
                    ]
                ],
                'status' => 500
            ], 500);
    }

    public function store(Request $request)
    {
        // validate input
        $this->validate($request, [
            'name' => 'required|unique:roles'
        ]);

        // create role
        $role = Role::create([
            'name' => $request->get('name')
        ]);

        return response()->json([
            'alerts' => [
                (object) [
                    'type' => 'success',
                    'title' => trans('chronos.scaffolding::alerts.Success.'),
                    'message' => trans('chronos.scaffolding::alerts.Role successfully created.'),
                ]
            ],
            'role' => $role,
            'status' => 200
        ], 200);
    }

    public function permissions_update(Request $request)
    {

        foreach (Role::all() as $role) {
            //assign permissions
            $role->permissions()->detach();

            if ($request->has('permission') && isset($request->input('permission')[$role->id])) {
                $role->permissions()->attach($request->input('permission')[$role->id]);
            }
        }

        return response()->json([
            'alerts' => [
                (object) [
                    'type' => 'success',
                    'title' => trans('chronos.scaffolding::alerts.Success.'),
                    'message' => trans('chronos.scaffolding::alerts.Permissions settings successfully saved.'),
                ]
            ],
            'role' => $role,
            'status' => 200
        ], 200);
    }

    public function update(Request $request, Role $role)
    {
        // validate input
        $this->validate($request, [
            'name' => [
                'required',
                Rule::unique('roles')->ignore($role->id)
            ]
        ]);

        $role->update([
            'name' => $request->get('name')
        ]);

        return response()->json([
            'alerts' => [
                (object) [
                    'type' => 'success',
                    'title' => trans('chronos.scaffolding::alerts.Success.'),
                    'message' => trans('chronos.scaffolding::alerts.Role successfully updated.'),
                ]
            ],
            'role' => $role,
            'status' => 200
        ], 200);
    }

    public function users(Request $request, Role $role)
    {
        $itemsPerPage = $request->has('perPage')
            ? $request->get('perPage') == 0 ? User::query()->count() : $request->get('perPage')
            : Config::get('chronos.items_per_page');

        $q = User::where('role_id', $role->id);

        // filter
        if ($request->has('filters')) {
            $filters = $request->get('filters');

            if (isset($filters['search']) && $filters['search'] != '') {
                $q->where('email', 'like', '%' . $filters['search'] . '%');
                $q->orWhere('firstname', 'like', '%' . $filters['search'] . '%');
                $q->orWhere('lastname', 'like', '%' . $filters['search'] . '%');
            }
        }

        // sort
        if ($request->has('sortBy') && $request->get('sortBy') != '') {
            $sortBy = $request->get('sortBy');
            $sortOrder = $request->get('sortOrder');

            switch ($sortBy) {
                case 'email':
                    $q->orderBy($sortBy, (isset($sortOrder) && $sortOrder === 'true') ? 'ASC' : 'DESC');
                    break;
                case 'firstname':
                    $q->orderBy($sortBy, (isset($sortOrder) && $sortOrder === 'true') ? 'ASC' : 'DESC');
                    break;
                case 'lastname':
                    $q->orderBy($sortBy, (isset($sortOrder) && $sortOrder === 'true') ? 'ASC' : 'DESC');
                    break;
                case 'name':
                    $q->orderBy('lastname', (isset($sortOrder) && $sortOrder === 'true') ? 'ASC' : 'DESC');
                    $q->orderBy('firstname', (isset($sortOrder) && $sortOrder === 'true') ? 'ASC' : 'DESC');
                    break;
                default:
                    return response()->json([
                        'alerts' => [
                            (object) [
                                'type' => 'error',
                                'title' => trans('chronos.scaffolding::alerts.Error.'),
                                'message' => trans('chronos.scaffolding::alerts.Invalid sortBy argument: :arg.', ['arg' => $sortBy]),
                            ]
                        ],
                        'status' => 200
                    ], 200);
            }
        }
        else
            $q->orderBy('email', 'ASC');

        // pagination
        $data = $q->paginate($itemsPerPage);


        return response()->json($data, 200);
    }

}
