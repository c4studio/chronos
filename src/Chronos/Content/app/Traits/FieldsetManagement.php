<?php

namespace Chronos\Content\Traits;

use Chronos\Content\Models\ContentField;
use Chronos\Content\Models\ContentFieldset;

trait FieldsetManagement
{

    private function createField($order, $data, $fieldset)
    {
        $additional_data = $this->getFieldAdditionalData($data);

        return ContentField::create([
            'fieldset_id' => $fieldset['id'],
            'machine_name' => $data['field_machine'],
            'name' => $data['name'],
            'type' => $data['type'],
            'widget' => $data['widget'],
            'default' => isset($data['default']) ? $data['default'] : '',
            'repeatable' => isset($data['repeatable']),
            'help_text' => $data['help_text'],
            'rules' => isset($data['rules']) ? $data['rules'] : '',
            'data' => $additional_data,
            'order' => $order
        ]);
    }

    private function createFieldset($order, $data, $parent)
    {
        return ContentFieldset::create([
            'parent_id' => $parent->id,
            'parent_type' => get_class($parent),
            'machine_name' => $data['fieldset_machine'],
            'name' => $data['name'],
            'description' => $data['description'],
            'repeatable' => isset($data['repeatable']),
            'order' => $order
        ]);
    }

    private function deleteFields($fieldset_id, $keep)
    {
        ContentField::where('fieldset_id', $fieldset_id)->whereNotIn('id', $keep)->delete();
    }

    private function deleteFieldsets($parent, $keep)
    {
        ContentFieldset::where('parent_type', get_class($parent))->where('parent_id', $parent->id)->whereNotIn('id', $keep)->delete();
    }

    private function getFieldAdditionalData($data)
    {
        $additional_data = null;

        switch ($data['type']) {
            case 'entity':
                $additional_data['entity_type'] = $data['entity_type'];

                break;
            case 'image':
                $additional_data['enable_alt'] = isset($data['enable_alt']);
                $additional_data['enable_title'] = isset($data['enable_title']);

                break;
            case 'list':
                $additional_data['values'] = $data['values'];

                break;
            case 'number':
                $additional_data['step'] = $data['step'];

                break;
        }

        if (!is_null($additional_data))
            return serialize($additional_data);
        else
            return null;
    }

    private function updateField($order, $data)
    {
        $field = ContentField::findOrFail($data['id']);

        $additional_data = $this->getFieldAdditionalData($data);

        $field->update([
            'machine_name' => $data['field_machine'],
            'name' => $data['name'],
            'type' => $data['type'],
            'widget' => $data['widget'],
            'default' => isset($data['default']) ? $data['default'] : '',
            'repeatable' => isset($data['repeatable']),
            'help_text' => $data['help_text'],
            'rules' => isset($data['rules']) ? $data['rules'] : '',
            'data' => $additional_data,
            'order' => $order
        ]);
    }

    private function updateFieldset($order, $data)
    {
        $fieldset = ContentFieldset::findOrFail($data['id']);

        $fieldset->update([
            'machine_name' => $data['fieldset_machine'],
            'name' => $data['name'],
            'description' => $data['description'],
            'repeatable' => isset($data['repeatable']),
            'order' => $order
        ]);
    }

    private function updateAll($request, $parent)
    {
        $keep_fieldsets = []; // every fieldset that will not be in this array, shall be deleted
        // check if we have any fieldsets and iterate through them
        if ($request->has('fieldsets')) {
            foreach ($request->get('fieldsets') as $fieldset_order => $fieldset_data) {
                $keep_fields = [];  // every field that will not be in this array, shall be deleted

                // update fieldset
                if (isset($fieldset_data['id'])) {
                    $this->updateFieldset($fieldset_order, $fieldset_data);

                    $keep_fieldsets[] = $fieldset_data['id'];
                }
                // create fieldset
                else {
                    $fieldset = $this->createFieldset($fieldset_order, $fieldset_data, $parent);

                    $fieldset_data['id'] = $fieldset->id;
                    $keep_fieldsets[] = $fieldset->id;
                }

                // check if we have any fields and iterate through them
                if (isset($fieldset_data['fields'])) {
                    foreach ($fieldset_data['fields'] as $field_order => $field_data) {
                        // update field
                        if (isset($field_data['id'])) {
                            $this->updateField($field_order, $field_data);

                            $keep_fields[] = $field_data['id'];
                        }
                        // create field
                        else {
                            $field = $this->createField($field_order, $field_data, $fieldset_data);

                            $keep_fields[] = $field->id;
                        }
                    }
                }

                // delete fields that have been removed
                $this->deleteFields($fieldset_data['id'], $keep_fields);
            }
        }

        // delete fieldsets that have been removed
        $this->deleteFieldsets($parent, $keep_fieldsets);
    }

    private function validateFieldsetRequest($request)
    {
        // validate input
        $this->validate($request, [
            'fieldsets.*.fieldset_machine' => 'required|distinct',
            'fieldsets.*.name' => 'required',
            'fieldsets.*.fields.*.field_machine' => 'required|distinct',
            'fieldsets.*.fields.*.name' => 'required',
            'fieldsets.*.fields.*.entity_type' => 'required_if:fieldsets.*.fields.*.type,entity',
            'fieldsets.*.fields.*.step' => 'numeric|min:0.0001',
            'fieldsets.*.fields.*.type' => 'required',
            'fieldsets.*.fields.*.values' => 'required_if:fieldsets.*.fields.*.type,list',
            'fieldsets.*.fields.*.widget' => 'required',
        ], [], [
            'fieldsets.*.fieldset_machine' => trans('chronos.content::forms.Fieldset machine name'),
            'fieldsets.*.name' => trans('chronos.content::forms.Fieldset name'),
            'fieldsets.*.fields.*.field_machine' => trans('chronos.content::forms.Field machine name'),
            'fieldsets.*.fields.*.name' => trans('chronos.content::forms.Field name'),
            'fieldsets.*.fields.*.entity_type' => trans('chronos.content::forms.Entity type'),
            'fieldsets.*.fields.*.step' => trans('chronos.content::forms.Step'),
            'fieldsets.*.fields.*.type' => trans('chronos.content::forms.Field type'),
            'fieldsets.*.fields.*.values' => trans('chronos.content::forms.Values'),
            'fieldsets.*.fields.*.widget' => trans('chronos.content::forms.Field widget'),
        ]);
    }

}