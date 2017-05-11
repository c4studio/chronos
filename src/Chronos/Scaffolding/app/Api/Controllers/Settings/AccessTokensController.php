<?php

namespace Chronos\Scaffolding\Api\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use Chronos\Scaffolding\Models\AccessToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Laravel\Passport\Token;

class AccessTokensController extends Controller
{

    public function index(Request $request)
    {
        $itemsPerPage = Config::get('chronos.items_per_page');

        $q = Token::query();

        // filter
        if ($request->has('filters')) {
            $filters = $request->get('filters');

            if (isset($filters['search']) && $filters['search'] != '') {
                $q->where('name', 'like', '%' . $filters['search'] . '%');
            }
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

        //add endpoints and access token value
        if (count($data) > 0) {
            foreach ($data as &$item) {
                //add admin URLs
                $item->setAttribute('endpoints', ['destroy' => route('api.settings.access_tokens.destroy', ['token' => $item->id])]);

                //add access token value
                $token = AccessToken::where('oauth_access_token_id', $item->id)->first();
                $item->setAttribute('access_token', !is_null($token) ? $token->token : null);
            }
        }

        return response()->json($data, 200);
    }

    public function destroy(Token $token)
    {
        if ($token->delete())
            return response()->json([
                'alerts' => [
                    (object) [
                        'type' => 'success',
                        'title' => trans('chronos.scaffolding::alerts.Success.'),
                        'message' => trans('chronos.scaffolding::alerts.Access token deletion was successful.'),
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
                        'message' => trans('chronos.scaffolding::alerts.Access token deletion was unsuccessful.'),
                    ]
                ],
                'status' => 500
            ], 500);
    }

    public function store(Request $request)
    {
        // validate input
        $this->validate($request, [
            'name' => 'required|unique:oauth_access_tokens'
        ]);

        // create access token
//        $token = \Auth::user()->createToken($request->get('name'));
        $root = User::whereHas('role', function($q) {
            $q->where('name', 'root');
        })->first();
        $token = $root->createToken($request->get('name'));
        $token = AccessToken::create(['oauth_access_token_id' => $token->token->id, 'token' => $token->accessToken]);

        return response()->json([
            'alerts' => [
                (object) [
                    'type' => 'success',
                    'title' => trans('chronos.scaffolding::alerts.Success.'),
                    'message' => trans('chronos.scaffolding::alerts.Access token successfully created.'),
                ]
            ],
            'token' => $token,
            'status' => 200
        ], 200);
    }

}
