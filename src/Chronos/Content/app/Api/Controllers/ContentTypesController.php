<?php

namespace Chronos\Content\Api\Controllers;

use App\Http\Controllers\Controller;
use Chronos\Content\Models\ContentField;
use Chronos\Content\Models\ContentFieldset;
use Chronos\Content\Models\ContentType;
use Chronos\Content\Traits\FieldsetManagement;
use Chronos\Scaffolding\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rule;

class ContentTypesController extends Controller
{
    use FieldsetManagement;

    public function index(Request $request)
    {
        $itemsPerPage = $request->has('perPage')
            ? $request->get('perPage') == 0 ? ContentType::query()->count() : $request->get('perPage')
            : Config::get('chronos.items_per_page');

        $q = ContentType::withCount('items');

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
                case 'items_count':
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

        // pagination
        $data = $q->paginate($itemsPerPage);



        return response()->json($data, 200);
    }

    public function destroy(ContentType $type)
    {
        //delete permissions
        Permission::where('name','view_content_type_' . $type->id)->first()->delete();
        Permission::where('name','add_content_type_' . $type->id)->first()->delete();
        Permission::where('name','edit_content_type_' . $type->id)->first()->delete();
        Permission::where('name','edit_content_type_fieldsets_' . $type->id)->first()->delete();
        Permission::where('name','delete_content_type_' . $type->id)->first()->delete();
        Permission::where('name','lock_content_type_delete_' . $type->id)->first()->delete();

        if ($type->delete())
            return response()->json([
                'alerts' => [
                    (object) [
                        'type' => 'success',
                        'title' => trans('chronos.content::alerts.Success.'),
                        'message' => trans('chronos.content::alerts.Content type successfully deleted.'),
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
                        'message' => trans('chronos.content::alerts.Content type deletion was unsuccessful.'),
                    ]
                ],
                'status' => 500
            ], 500);
    }

    public function destroy_field(ContentField $field)
    {
        if ($field->delete())
            return response()->json([
                'alerts' => [
                    (object) [
                        'type' => 'success',
                        'title' => trans('chronos.content::alerts.Success.'),
                        'message' => trans('chronos.content::alerts.Field successfully deleted.'),
                    ]
                ],
                'status' => 200
            ], 200);
        else
            return response(null, 500);
    }

    public function destroy_fieldset(ContentFieldset $fieldset)
    {
        if ($fieldset->delete())
            return response()->json([
                'alerts' => [
                    (object) [
                        'type' => 'success',
                        'title' => trans('chronos.content::alerts.Success.'),
                        'message' => trans('chronos.content::alerts.Fieldset successfully deleted.'),
                    ]
                ],
                'status' => 200
            ], 200);
        else
            return response(null, 500);
    }

    public function fieldset(Request $request, ContentType $type)
    {
        $this->validateFieldsetRequest($request);

        // let fieldset management handle update
        $this->updateAll($request, $type);

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

    public function show(Request $request, ContentType $type)
    {
        if ($request->has('load') && $request->get('load') == 'fieldsets')
            $type->load('fieldsets');

        return response()->json($type, 200);
    }

    public function store(Request $request)
    {
        // validate input
        $this->validate($request, [
            'name' => 'required|unique:content_types',
            'title_label' => 'required'
        ]);

        // create content type
        $type = ContentType::create([
            'name' => $request->get('name'),
            'title_label' => $request->get('title_label'),
            'translatable' => $request->has('translatable'),
            'notes' => $request->get('notes'),
        ]);

        //create permissions
        Permission::create(['name' => 'view_content_type_' . $type->id, 'label' => trans('chronos.content::permissions.View :name' , ['name' => str_plural(strtolower($type->name))]), 'order' => 10]);
        Permission::create(['name' => 'add_content_type_' . $type->id, 'label' => trans('chronos.content::permissions.Add :name' , ['name' => str_plural(strtolower($type->name))]), 'order' => 10]);
        Permission::create(['name' => 'edit_content_type_' . $type->id, 'label' => trans('chronos.content::permissions.Edit :name' , ['name' => str_plural(strtolower($type->name))]), 'order' => 10]);
        Permission::create(['name' => 'edit_content_type_fieldsets_' . $type->id, 'label' => trans('chronos.content::permissions.Edit :name fieldsets' , ['name' => str_plural(strtolower($type->name))]), 'order' => 10]);
        Permission::create(['name' => 'lock_content_type_delete_' . $type->id, 'label' => trans('chronos.content::permissions.Lock :name delete' , ['name' => str_plural(strtolower($type->name))]), 'order' => 10]);
        Permission::create(['name' => 'delete_content_type_' . $type->id, 'label' => trans('chronos.content::permissions.Delete :name' , ['name' => str_plural(strtolower($type->name))]), 'order' => 10]);

        return response()->json([
            'alerts' => [
                (object) [
                    'type' => 'success',
                    'title' => trans('chronos.content::alerts.Success.'),
                    'message' => trans('chronos.content::alerts.Content type successfully created.'),
                ]
            ],
            'type' => $type,
            'status' => 200
        ], 200);
    }

    public function update(Request $request, ContentType $type)
    {
        // validate input
        $this->validate($request, [
            'name' => [
                'required',
                Rule::unique('content_types')->ignore($type->id)
            ],
            'title_label' => 'required'
        ]);

        // handle update
        $type->update([
            'name' => $request->get('name'),
            'title_label' => $request->get('title_label'),
            'translatable' => $request->has('translatable'),
            'notes' => $request->get('notes')
        ]);

        return response()->json([
            'alerts' => [
                (object) [
                    'type' => 'success',
                    'title' => trans('chronos.content::alerts.Success.'),
                    'message' => trans('chronos.content::alerts.Content type successfully updated.'),
                ]
            ],
            'type' => $type,
            'status' => 200
        ], 200);
    }

}
