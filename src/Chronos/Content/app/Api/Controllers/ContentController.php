<?php

namespace Chronos\Content\Api\Controllers;

use App\Http\Controllers\Controller;
use Chronos\Content\Models\Content;
use Chronos\Content\Models\ContentField;
use Chronos\Content\Models\ContentFieldData;
use Chronos\Content\Models\ContentFieldset;
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

            if (isset($filters['language']) && $filters['language'] != '')
                $q->where('language', $filters['language']);
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
            $q->active();

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
            'author_id' => $request->get('author_id'),
            'language' => $request->has('language') ? $request->get('language') : \Config::get('app.locale'),
            'lock_delete' => $request->has('lock_delete'),
            'parent_id' => $request->has('parent_id') && $request->get('parent_id') != 0 ? $request->get('parent_id') : null,
            'slug' => $request->get('slug'),
            'status' => $request->get('status'),
            'title' => $request->get('title'),
            'type_id' => $type->id
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

    public function translate(Request $request, ContentType $type, Content $content)
    {
        // duplicate content
        $translation = $content->replicate();
        $translation->language = $request->get('language');
        $translation->translation_id = $content->translation_id == null ? $content->id : $content->translation_id;
        $translation->push();

        // duplicate content fieldset
        $fieldsets = ContentFieldset::where('parent_id', $content->id)->where('parent_type', 'Chronos\Content\Models\Content')->get();
        foreach ($fieldsets as $fieldset) {
            $translation_fieldset = $fieldset->replicate();
            $translation_fieldset->parent_id = $translation->id;
            $translation_fieldset->push();

            $fields = ContentField::where('fieldset_id', $fieldset->id)->get();
            foreach ($fields as $field) {
                $translation_field = $field->replicate();
                $translation_field->fieldset_id = $translation_fieldset->id;
                $translation_field->push();

                $fields_data = ContentFieldData::where('field_id', $field->id)->get();
                foreach ($fields_data as $field_data) {
                    $translation_field_data = $field_data->replicate();
                    $translation_field_data->content_id = $translation->id;
                    $translation_field_data->field_id = $translation_field->id;
                    $translation_field_data->push();
                }
            }
        }

        // duplicate content fields data
        $fields_data = ContentFieldData::whereHas('field', function($q1) {
            $q1->whereHas('fieldset', function($q2) {
                $q2->where('parent_type', 'Chronos\Content\Models\ContentType');
            });
        })->where('content_id', $content->id)->get();
        foreach ($fields_data as $field_data) {
            $translation_field_data = $field_data->replicate();
            $translation_field_data->content_id = $translation->id;
            $translation_field_data->push();
        }

        return redirect($translation->admin_urls['edit']);
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
