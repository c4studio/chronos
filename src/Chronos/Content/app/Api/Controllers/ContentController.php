<?php

namespace Chronos\Content\Api\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Chronos\Content\Models\Content;
use Chronos\Content\Models\ContentField;
use Chronos\Content\Models\ContentFieldData;
use Chronos\Content\Models\ContentFieldset;
use Chronos\Content\Models\ContentType;
use Chronos\Content\Models\Media;
use Chronos\Content\Traits\ContentManagement;
use Chronos\Content\Traits\FieldsetManagement;
use Chronos\Scaffolding\Generators\SeedGenerator;
use Illuminate\Foundation\Auth\User;
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
                case 'created_at':
                    $q->orderBy($sortBy, (isset($sortOrder) && $sortOrder === 'true') ? 'ASC' : 'DESC');
                    break;
                case 'status':
                    $q->orderBy($sortBy, (isset($sortOrder) && $sortOrder === 'true') ? 'DESC' : 'ASC');
                    break;
                case 'title':
                    $q->orderBy($sortBy, (isset($sortOrder) && $sortOrder === 'true') ? 'ASC' : 'DESC');
                    break;
                case 'updated_at':
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

    public function destroy_bulk(Request $request, ContentType $type)
    {
        $deleted_content_count = 0;

        if ($request->has('content')) {
            foreach ($request->get('content') as $type_id) {
                $content = Content::find($type_id);

                if (!$content->lock_delete && $content->delete())
                    $deleted_content_count++;
            }
        }

        if ($deleted_content_count > 0) {
            return response()->json([
                'alerts' => [
                    (object)[
                        'type' => 'success',
                        'title' => trans('chronos.content::alerts.Success.'),
                        'message' => trans_choice('chronos.content::alerts.:count items deleted.', $deleted_content_count, ['count' => $deleted_content_count])
                    ]
                ],
                'status' => 200
            ], 200);
        } else {
            return response()->json([
                'alerts' => [
                    (object)[
                        'type' => 'warning',
                        'title' => trans('chronos.content::alerts.Warning.'),
                        'message' => trans_choice('chronos.content::alerts.:count items deleted.', $deleted_content_count, ['count' => $deleted_content_count])
                    ]
                ],
                'status' => 200
            ], 200);
        }
    }

    public function export(Request $request, ContentType $type)
    {
        if (!$request->has('content'))
            $content = Content::all();
        else
            $content = Content::whereIn('id', $request->get('content'))->get();

        $generator = new SeedGenerator();
        $generator->setFilename(studly_case($type->name) . 'ContentTableSeeder.php');
        $generator->setClassName(studly_case($type->name) . 'ContentTableSeeder');

        $generator->addUses('Chronos\Content\Models\Content');
        $generator->addUses('Chronos\Content\Models\ContentField');
        $generator->addUses('Chronos\Content\Models\ContentFieldData');
        $generator->addUses('Chronos\Content\Models\ContentFieldset');
        $generator->addUses('Chronos\Content\Models\ContentType');
        $generator->addUses('Chronos\Content\Models\Media');

        foreach ($content as $key => $item) {
            // create content
            $generator->addIndent(2);
            $generator->addContent('/*');
            $generator->addNewLines();
            $generator->addIndent(2);
            $generator->addContent(' * ' . $item->title);
            $generator->addNewLines();
            $generator->addIndent(2);
            $generator->addContent(' * /' . $item->slug);
            $generator->addNewLines();
            $generator->addIndent(2);
            $generator->addContent(' */');
            $generator->addNewLines();

            $generator->addIndent(2);
            $generator->addContent('$type = ContentType::where(\'name\', \'' . $item->type->name . '\')->first();');
            $generator->addNewLines();

            if ($item->translation_id) {
                $translation = Content::find($item->translation_id);
                $generator->addIndent(2);
                $generator->addContent('$translation = Content::where(\'slug\', \'' . $translation->slug . '\')->where(\'type_id\', ' . $translation->type_id . ')->first();');
                $generator->addNewLines();
            }

            $generator->addIndent(2);
            $generator->addContent('$content = Content::create([');
            $generator->addNewLines();

            $generator->addIndent(3);
            $generator->addContent('\'type_id\' => $type->id,');
            $generator->addNewLines();

            $except = ['translation_id', 'type_id'];
            foreach ($item->getFillable() as $attribute) {
                if (!in_array($attribute, $except)) {
                    $value = is_string($item->{$attribute}) ? '"' . addslashes(normalize_newline($item->{$attribute})) . '"' :
                        (is_null($item->{$attribute}) ? 'null' : $item->{$attribute});
                    $generator->addIndent(3);
                    $generator->addContent('\'' . $attribute . '\' => ' . $value . ',');
                    $generator->addNewLines();
                }
            }

            if ($item->translation_id) {
                $generator->addIndent(3);
                $generator->addContent('\'translation_id\' => $translation ? $translation->id : null,');
                $generator->addNewLines();
            }

            $generator->addIndent(2);
            $generator->addContent(']);');
            $generator->addNewLines(2);

            // create private fieldsets
            $fieldsets = ContentFieldset::where('parent_type', 'Chronos\Content\Models\Content')->where('parent_id', $item->id)->get();

            foreach ($fieldsets as $fieldset) {
                $generator->addIndent(2);
                $generator->addContent('$fieldset = ContentFieldset::create([');
                $generator->addNewLines();

                $generator->addIndent(3);
                $generator->addContent('\'parent_id\' => $content->id,');
                $generator->addNewLines();

                $except = ['parent_id'];
                foreach ($fieldset->getFillable() as $attribute) {
                    if (!in_array($attribute, $except)) {
                        $value = is_string($fieldset->{$attribute}) ? '"' . addslashes(normalize_newline($fieldset->{$attribute})) . '"' :
                            (is_null($fieldset->{$attribute}) ? 'null' : $fieldset->{$attribute});
                        $generator->addIndent(3);
                        $generator->addContent('\'' . $attribute . '\' => ' . $value . ',');
                        $generator->addNewLines();
                    }
                }

                $generator->addIndent(2);
                $generator->addContent(']);');
                $generator->addNewLines(2);

                // create fields
                $fields = ContentField::where('fieldset_id', $fieldset->id)->get();

                foreach ($fields as $field) {
                    $generator->addIndent(2);
                    $generator->addContent('ContentField::create([');
                    $generator->addNewLines();

                    $generator->addIndent(3);
                    $generator->addContent('\'fieldset_id\' => $fieldset->id,');
                    $generator->addNewLines();

                    $except = ['fieldset_id'];
                    foreach ($field->getFillable() as $attribute) {
                        if (!in_array($attribute, $except)) {
                            $value = is_string($field->{$attribute}) ? '"' . addslashes(normalize_newline($field->{$attribute})) . '"' :
                                (is_null($field->{$attribute}) ? 'null' : $field->{$attribute});
                            $generator->addIndent(3);
                            $generator->addContent('\'' . $attribute . '\' => ' . $value . ',');
                            $generator->addNewLines();
                        }
                    }

                    $generator->addIndent(2);
                    $generator->addContent(']);');
                    $generator->addNewLines(2);
                }
            }

            // add content data
            foreach ($item->all_fieldsets as $fieldset) {
                foreach ($fieldset->fields as $field) {
                    $generator->addIndent(2);
                    if ($fieldset->parent_type == 'Chronos\Content\Models\ContentType') {
                        $parent = ContentType::find($fieldset->parent_id);
                        $generator->addContent('$parent_id = ' . $fieldset->parent_type . '::where(\'name\', \'' . $parent->name . '\')->first()->id;');
                    }
                    else {
                        $parent = Content::find($fieldset->parent_id);
                        $generator->addContent('$parent_id = ' . $fieldset->parent_type . '::where(\'slug\', \'' . $parent->slug . '\')->first()->id;');
                    }
                    $generator->addNewLines();
                    $generator->addIndent(2);
                    $generator->addContent('$field_id = ContentField::where(\'machine_name\', \'' . $field->machine_name . '\')->whereHas(\'fieldset\', function($q) use ($parent_id) {');
                    $generator->addNewLines();
                    $generator->addIndent(3);
                    $generator->addContent('$q->where(\'parent_id\', $parent_id)->where(\'parent_type\', \'' . $fieldset->parent_type .'\')->where(\'machine_name\', \'' . $fieldset->machine_name . '\');');
                    $generator->addNewLines();
                    $generator->addIndent(2);
                    $generator->addContent('})->first()->id;');
                    $generator->addNewLines(2);

                    $content_data = ContentFieldData::where('content_id', $item->id)->where('field_id', $field->id)->get();

                    foreach ($content_data as $data) {
                        // check if media
                        if ($field->widget == 'media') {
                            $media = Media::find(unserialize($data->value)['media_id']);
                            if ($media) {
                                $generator->addIndent(2);
                                $generator->addContent('$media = Media::where(\'basename\', \'' . $media->basename . '\')->first();');
                                $generator->addNewLines();

                                $generator->addIndent(2);
                                $generator->addContent('if ($media) {');
                                $generator->addNewLines();

                                $generator->addIndent(3);
                                $generator->addContent('ContentFieldData::create([');
                                $generator->addNewLines();

                                $generator->addIndent(4);
                                $generator->addContent('\'content_id\' => $content->id,');
                                $generator->addNewLines();
                                $generator->addIndent(4);
                                $generator->addContent('\'fieldset_repetition_key\' => ' . $data->fieldset_repetition_key . ',');
                                $generator->addNewLines();
                                $generator->addIndent(4);
                                $generator->addContent('\'field_id\' => $field_id,');
                                $generator->addNewLines();
                                $generator->addIndent(4);
                                $generator->addContent('\'field_repetition_key\' => ' . $data->field_repetition_key . ',');
                                $generator->addNewLines();
                                $generator->addIndent(4);
                                $generator->addContent('\'value\' => preg_replace(\'/((?:.*)s:8:"media_id";s:(?:[0-9]):")([0-9]+)("(?:.*))/\', \'$01\' . $media->id . \'$03\', \'' . $data->value . '\')');
                                $generator->addNewLines();

                                $generator->addIndent(3);
                                $generator->addContent(']);');
                                $generator->addNewLines();

                                $generator->addIndent(2);
                                $generator->addContent('}');
                                $generator->addNewLines(2);
                            }
                        }
                        // check if entity
                        elseif ($field->type == 'entity') {
                            $entity_data = unserialize($field->data);
                            $ids = is_numeric($data->value) ? [$data->value] : unserialize($data->value);

                            if (!is_array($ids))
                                $ids = [];

                            if ($entity_data['entity_type'] == '\\' . ltrim(Config::get('auth.providers.users.model'), '\\')) {
                                $entities = User::whereIn('id', $ids)->get()->pluck('email')->toArray();

                                $generator->addIndent(2);
                                $generator->addContent('$entities = ' . '\\' . ltrim(Config::get('auth.providers.users.model'), '\\') . '::whereIn(\'email\', [\'' . implode('\', \'', $entities) . '\'])->get();');
                                $generator->addNewLines();
                            }
                            else {
                                $entities = Content::whereIn('id', $ids)->get()->pluck('slug')->toArray();
                                list($type_model, $type_id) = explode(':', $entity_data['entity_type']);

                                $generator->addIndent(2);
                                $generator->addContent('$entities = Content::where(\'type_id\', ' . $type_id . ')->whereIn(\'slug\', [\'' . implode('\', \'', $entities) . '\'])->get();');
                                $generator->addNewLines();
                            }

                            $generator->addIndent(2);
                            $generator->addContent('if ($entities->count()) {');
                            $generator->addNewLines();

                            $generator->addIndent(3);
                            $generator->addContent('ContentFieldData::create([');
                            $generator->addNewLines();

                            $generator->addIndent(4);
                            $generator->addContent('\'content_id\' => $content->id,');
                            $generator->addNewLines();
                            $generator->addIndent(4);
                            $generator->addContent('\'fieldset_repetition_key\' => ' . $data->fieldset_repetition_key . ',');
                            $generator->addNewLines();
                            $generator->addIndent(4);
                            $generator->addContent('\'field_id\' => $field_id,');
                            $generator->addNewLines();
                            $generator->addIndent(4);
                            $generator->addContent('\'field_repetition_key\' => ' . $data->field_repetition_key . ',');
                            $generator->addNewLines();
                            if ($field->widget == 'autocomplete') {
                                $generator->addIndent(4);
                                $generator->addContent('\'value\' => $entities[0]->id');
                                $generator->addNewLines();
                            }
                            else {
                                $generator->addIndent(4);
                                $generator->addContent('\'value\' => serialize($entities->pluck(\'id\')->toArray())');
                                $generator->addNewLines();
                            }

                            $generator->addIndent(3);
                            $generator->addContent(']);');
                            $generator->addNewLines();

                            $generator->addIndent(2);
                            $generator->addContent('}');
                            $generator->addNewLines(2);
                        }
                        // default behaviour
                        else {
                            $generator->addIndent(2);
                            $generator->addContent('ContentFieldData::create([');
                            $generator->addNewLines();

                            $generator->addIndent(3);
                            $generator->addContent('\'content_id\' => $content->id,');
                            $generator->addNewLines();
                            $generator->addIndent(3);
                            $generator->addContent('\'fieldset_repetition_key\' => ' . $data->fieldset_repetition_key . ',');
                            $generator->addNewLines();
                            $generator->addIndent(3);
                            $generator->addContent('\'field_id\' => $field_id,');
                            $generator->addNewLines();
                            $generator->addIndent(3);
                            $generator->addContent('\'field_repetition_key\' => ' . $data->field_repetition_key . ',');
                            $generator->addNewLines();
                            $generator->addIndent(3);
                            $generator->addContent('\'value\' => \'' . $data->value . '\'');
                            $generator->addNewLines();

                            $generator->addIndent(2);
                            $generator->addContent(']);');
                            $generator->addNewLines(2);
                        }
                    }
                }
            }

            if ($key < count($content) - 1)
                $generator->addNewLines(3);
        }

        $generator->generateFile();
        $generator->downloadFile();
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

        $status_scheduled =
            $request->has('status_scheduled') ?
            Carbon::parse($request->get('status_scheduled'), new \DateTimeZone($request->get('status_scheduled_timezone_offset') / 60))->timestamp :
            null;

        // handle store
        $content = Content::create([
            'author_id' => $request->get('author_id'),
            'language' => $request->has('language') ? $request->get('language') : \Config::get('app.locale'),
            'lock_delete' => $request->has('lock_delete'),
            'parent_id' => $request->has('parent_id') && $request->get('parent_id') != 0 ? $request->get('parent_id') : null,
            'slug' => $request->get('slug'),
            'status' => $request->get('status'),
            'status_scheduled' => $status_scheduled,
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

        $status_scheduled =
            $request->has('status_scheduled') ?
            Carbon::parse($request->get('status_scheduled'))->addHours($request->get('status_scheduled_timezone_offset') / 60) :
            null;

        // handle update
        $content->update([
            'parent_id' => $request->has('parent_id') && $request->get('parent_id') != 0 ? $request->get('parent_id') : null,
            'slug' => $request->get('slug'),
            'title' => $request->get('title'),
            'order' => $request->get('order'),
            'status' => $request->get('status'),
            'status_scheduled' => $status_scheduled,
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
