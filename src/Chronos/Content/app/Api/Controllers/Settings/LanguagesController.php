<?php

namespace Chronos\Content\Api\Controllers\Settings;

use App\Http\Controllers\Controller;
use Chronos\Content\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Config;

class LanguagesController extends Controller
{

    public function index(Request $request)
    {
        $itemsPerPage = $request->has('perPage')
            ? $request->get('perPage') == 0 ? Language::query()->count() : $request->get('perPage')
            : Config::get('chronos.items_per_page');

        $q = Language::query();

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
                                'title' => trans('chronos.content::alerts.Error.'),
                                'message' => trans('chronos.content::alerts.Invalid sortBy argument: :arg.', ['arg' => $sortBy]),
                            ]
                        ],
                        'status' => 200
                    ], 200);
            }
        }
        else
            $q->orderBy('name', 'ASC');

        // withInactive
        if (!$request->has('withInactive') || !$request->get('withInactive'))
            $q->active();

        // pagination
        $data = $q->paginate($itemsPerPage);

        return response()->json($data, 200);
    }

    public function activate(Language $language)
    {
        $language->status = 1;
        $language->save();

        return response()->json([
            'alerts' => [
                (object) [
                    'type' => 'success',
                    'title' => trans('chronos.content::alerts.Success.'),
                    'message' => trans('chronos.content::alerts.Language successfully activated.'),
                ]
            ],
            'language' => $language,
            'status' => 200
        ], 200);
    }

    public function all(Request $request)
    {
        $languages = collect(Config::get('languages.list'));

        $itemsPerPage = $request->has('perPage')
            ? $request->get('perPage') == 0 ? $languages->count() : $request->get('perPage')
            : Config::get('chronos.items_per_page');

        // filter
        if ($request->has('filters')) {
            $filters = $request->get('filters');

            if (isset($filters['search']) && $filters['search'] != '')
                $languages->filter(function($language) use ($filters) {
                    return strpos($language, $filters['search']) !== false;
                });
        }

        // sort
        if ($request->has('sortBy') && $request->get('sortBy') != '') {
            $sortBy = $request->get('sortBy');
            $sortOrder = $request->get('sortOrder');

            switch ($sortBy) {
                case 'code':
                    $languages = $languages->toArray();
                    ksort($languages);
                    $languages = collect($languages);

                    if ($sortOrder == 'DESC')
                        $languages = $languages->reverse();

                    break;
                case 'name':
                    $languages->sort();

                    if ($sortOrder == 'DESC')
                        $languages = $languages->reverse();

                    break;
                default:
                    return response()->json([
                        'alerts' => [
                            (object) [
                                'type' => 'error',
                                'title' => trans('chronos.content::alerts.Error.'),
                                'message' => trans('chronos.content::alerts.Invalid sortBy argument: :arg.', ['arg' => $sortBy]),
                            ]
                        ],
                        'status' => 200
                    ], 200);
            }
        }

        $data = new LengthAwarePaginator($languages->forPage($request->has('page') ? $request->get('page') : 1, $itemsPerPage), $languages->count(), $itemsPerPage, $request->has('page') ? $request->get('page') : 1);

        return response()->json($data, 200);
    }

    public function deactivate(Language $language)
    {
        $language->status = 0;
        $language->save();

        return response()->json([
            'alerts' => [
                (object) [
                    'type' => 'success',
                    'title' => trans('chronos.content::alerts.Success.'),
                    'message' => trans('chronos.content::alerts.Language successfully deactivated.'),
                ]
            ],
            'language' => $language,
            'status' => 200
        ], 200);
    }

    public function store(Request $request)
    {
        // validate input
        $this->validate($request, [
            'code' => 'required|unique:languages'
        ], [
            'required' => trans('chronos.content::alerts.You must select a language.'),
            'unique' => trans('chronos.content::alerts.You have already added this language.')
        ]);

        // create languages
        $languages = Config::get('languages.list');
        $lang = array_search($request->get('code'), array_column($languages, 'code', 'name'));
        $language = Language::create([
            'code' => $request->get('code'),
            'name' => $lang
        ]);

        return response()->json([
            'alerts' => [
                (object) [
                    'type' => 'success',
                    'title' => trans('chronos.content::alerts.Success.'),
                    'message' => trans('chronos.content::alerts.Language successfully added.'),
                ]
            ],
            'language' => $language,
            'status' => 200
        ], 200);
    }

}
