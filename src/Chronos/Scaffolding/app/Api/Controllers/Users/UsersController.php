<?php

namespace Chronos\Scaffolding\Api\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;


class UsersController extends Controller
{
    public function index(Request $request)
    {
        $itemsPerPage = $request->has('perPage')
            ? $request->get('perPage') == 0 ? User::query()->count() : $request->get('perPage')
            : Config::get('chronos.items_per_page');

        $q = User::query();

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
