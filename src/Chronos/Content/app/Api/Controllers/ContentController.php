<?php

namespace Chronos\Content\Api\Controllers;

use App\Http\Controllers\Controller;
use Chronos\Content\Models\Content;
use Chronos\Content\Models\ContentType;
use Chronos\Content\Traits\ContentManagement;
use Chronos\Content\Traits\FieldsetManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class ContentController extends Controller
{
    use ContentManagement;
    use FieldsetManagement;

    public function index(Request $request, ContentType $type)
    {
        $itemsPerPage = $request->has('perPage')
                            ? $request->get('perPage') == 0 ? Content::query()->count() : $request->get('perPage')
                            : Config::get('chronos.items_per_page');

        $q = Content::where('type_id', $type->id);

        // filter
        if ($request->has('filters')) {
            $filters = $request->get('filters');

            if (isset($filters['search']) && $filters['search'] != '')
                $q->where('title', 'like', '%' . $filters['search'] . '%');
        }

        // hierarchy
        if ($request->has('hierarchy') && $request->get('hierarchy') == 1)
            $q->whereNull('parent_id');

        // sort
        if ($request->has('sortBy') && $request->get('sortBy') != '') {
            $sortBy = $request->get('sortBy');
            $sortOrder = $request->get('sortOrder');

            switch ($sortBy) {
                case 'status':
                    $q->orderBy($sortBy, (isset($sortOrder) && $sortOrder === 'true') ? 'DESC' : 'ASC');
                    break;
                case 'title':
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
            $q->orderBy('title', 'ASC');

        // withInactive
        if (!$request->has('withInactive') || !$request->get('withInactive'))
            $q->where('status', 1);

        // pagination
        $data = $q->paginate($itemsPerPage);

        return response()->json($data, 200);
    }

    public function destroy(ContentType $type, Content $content)
    {
        if ($content->delete())
            return response()->json([
                'alerts' => [
                    (object) [
                        'type' => 'success',
                        'title' => trans('chronos.content::alerts.Success.'),
                        'message' => trans('chronos.content::alerts.:type successfully deleted.', ['type' => $type->name]),
                    ]
                ],
                'status' => 200
            ], 200);
        else
            return response()->json([
                'alerts' => [
                    (object) [
                        'type' => 'error',
                        'title' => trans('chronos.content::alerts.Error.'),
                        'message' => trans('chronos.content::alerts.:type deletion was unsuccessful.', ['type' => $type->name]),
                    ]
                ],
                'status' => 500
            ], 500);
    }

    public function fieldset(Request $request, ContentType $type, Content $content)
    {
        $this->validateFieldsetRequest($request);

        // let fieldset management handle update
        $this->updateAll($request, $content);

        return response()->json([
            'alerts' => [
                (object) [
                    'type' => 'success',
                    'title' => trans('chronos.content::alerts.Success.'),
                    'message' => trans('chronos.content::alerts.Fieldsets successfully updated.'),
                ]
            ],
            'status' => 200
        ], 200);
    }

    public function show(Request $request, ContentType $type, Content $content)
    {
        if ($request->has('load') && $request->get('load') == 'allFieldsets')
            $content->append('allFieldsets');

        if ($request->has('load') && $request->get('load') == 'fieldsets')
            $content->load('fieldsets');

        return response()->json($content, 200);
    }

    public function store(Request $request, ContentType $type)
    {
        $type->load('fieldsets');

        $this->validateContentRequest($request, $type);

        // handle store
        $content = Content::create([
            'type_id' => $type->id,
            'parent_id' => $request->has('parent_id') && $request->get('parent_id') != 0 ? $request->get('parent_id') : null,
            'slug' => $request->get('slug'),
            'title' => $request->get('title'),
            'status' => $request->get('status'),
            'lock_delete' => $request->has('lock_delete'),
            'author_id' => $request->get('author_id')
        ]);

        $this->insertFieldData($request, $content);

        return response()->json([
            'alerts' => [
                (object) [
                    'type' => 'success',
                    'title' => trans('chronos.content::alerts.Success.'),
                    'message' => trans('chronos.content::alerts.:type successfully created.', ['type' => $type->name]),
                ]
            ],
            'content' => $content,
            'status' => 200
        ], 200);
    }

    public function update(Request $request, ContentType $type, Content $content)
    {
        $type->load('fieldsets');

        $this->validateContentRequest($request, $type, $content);

        // handle update
        $content->update([
            'parent_id' => $request->has('parent_id') && $request->get('parent_id') != 0 ? $request->get('parent_id') : null,
            'slug' => $request->get('slug'),
            'title' => $request->get('title'),
            'order' => $request->get('order'),
            'status' => $request->get('status'),
            'lock_delete' => $request->has('lock_delete')
        ]);

        $this->updateFieldData($request, $content);

        return response()->json([
            'alerts' => [
                (object) [
                    'type' => 'success',
                    'title' => trans('chronos.content::alerts.Success.'),
                    'message' => trans('chronos.content::alerts.:type successfully updated.', ['type' => $type->name]),
                ]
            ],
            'content' => $content,
            'status' => 200
        ], 200);
    }

}
