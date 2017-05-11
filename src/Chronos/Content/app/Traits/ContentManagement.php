<?php

namespace Chronos\Content\Traits;

use Chronos\Content\Models\ContentField;
use Chronos\Content\Models\ContentFieldData;
use Illuminate\Validation\Rule;

trait ContentManagement
{

    private function deleteFieldData($content)
    {
        ContentFieldData::where('content_id', $content->id)->delete();
    }

    private function insertFieldData($request, $content)
    {
        if ($request->has('fields')) {
            foreach ($request->get('fields') as $fieldset_id => $fieldset_repetition) {
                foreach ($fieldset_repetition as $fieldset_repetition_key => $field) {
                    foreach ($field as $field_id => $field_repetition) {
                        foreach ($field_repetition as $field_repetition_key => $field_value) {
                            $field_model = ContentField::findOrFail($field_id);
                            switch ($field_model->widget) {
                                case 'checkbox':
                                case 'tagging':
                                    $value = serialize($field_value);
                                    break;
                                case 'media':
                                    $value = serialize([
                                        'media_id' => $field_value['media_id'],
                                        'alt' => $field_model->enable_alt && isset($field_value['alt']) ? $field_value['alt'] : null,
                                        'title' => $field_model->enable_alt && isset($field_value['title']) ? $field_value['title'] : null
                                    ]);
                                    break;
                                default:
                                    $value = $field_value;
                            }

                            ContentFieldData::create([
                                'content_id' => $content->id,
                                'fieldset_repetition_key' => (int) $fieldset_repetition_key,
                                'field_id' => $field_model->id,
                                'field_repetition_key' => (int) $field_repetition_key,
                                'value' => $value
                            ]);
                        }
                    }
                }
            }
        }
    }

    private function updateFieldData($request, $content)
    {
        $this->deleteFieldData($content);
        $this->insertFieldData($request, $content);
    }

    private function validateContentRequest($request, $type, $content = null)
    {
        // build rules and field names array
        $slug_rule = Rule::unique('content')->where(function($query) use ($type) {
            $query->where('type_id', $type->id);
        });

        if ($request->isMethod('PATCH') && $content) {
            $slug_rule->where(function($query) use ($content) {
                $query->where('language', $content->language);
            });
            $slug_rule = $slug_rule->ignore($content->id);
        }

        $rules = [
            'title' => 'required',
            'slug' => [
                'required', $slug_rule
            ],
            'author_id' => 'required|exists:users,id',
        ];
        $field_names = [
            'title' => $type->title_label,
            'slug' => trans('chronos.content::forms.Slug')
        ];

        $all_fieldsets = [];

        if ($type->fieldsets)
            $all_fieldsets = $type->fieldsets;
        if ($content && $content->fieldsets)
            $all_fieldsets = $all_fieldsets->merge($content->fieldsets);

        foreach ($all_fieldsets as $fieldset) {
            foreach ($fieldset->fields as $field) {
                $default_field_rules = '';
                switch ($field->widget) {
                    case 'email':
                        $default_field_rules .= 'email';
                        break;
                    case 'number':
                        $default_field_rules .= 'numeric';
                        break;
                    case 'url':
                        $default_field_rules .= 'url';
                        break;
                }

                // repeatable fieldset
                if (isset($_POST['fields'][$fieldset->id])) {
                    foreach ($_POST['fields'][$fieldset->id] as $fieldset_repetition_key => $fieldset_repetition) {
                        // repeatable field
                        if (isset($fieldset_repetition[$field->id])) {
                            foreach ($fieldset_repetition[$field->id] as $field_repetition_key => $field_repetition) {
                                $rules['fields.' . $fieldset->id . '.' . $fieldset_repetition_key . '.' . $field->id . '.' . $field_repetition_key] = implode('|', array_filter([$field->rules, $default_field_rules]));
                                $field_names['fields.' . $fieldset->id . '.' . $fieldset_repetition_key . '.' . $field->id . '.' . $field_repetition_key] = $field->name;
                            }
                        }
                        // non-repeatable field
                        else {
                            $rules['fields.' . $fieldset->id . '.' . $fieldset_repetition_key . '.' . $field->id . '.0'] = implode('|', array_filter([$field->rules, $default_field_rules]));
                            $field_names['fields.' . $fieldset->id . '.' . $fieldset_repetition_key . '.' . $field->id . '.0'] = $field->name;
                        }
                    }
                }
                // non-repeatable fieldset
                else {
                    $rules['fields.' . $fieldset->id . '.0.' . $field->id . '.0'] = implode('|', array_filter([$field->rules, $default_field_rules]));
                    $field_names['fields.' . $fieldset->id . '.0.' . $field->id . '.0'] = $field->name;
                }
            }
        }

        // validate input
        $this->validate($request, $rules, [], $field_names);
    }

}