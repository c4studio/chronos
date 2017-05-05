<?php

namespace Chronos\Scaffolding\Traits;

use Illuminate\Validation\Rule;

trait ImageStyleManagement
{

    private function getImageStyleData($request)
    {
        $data = [
            'crop_height' => $request->get('crop_height') != '' ? $request->get('crop_height') : null,
            'crop_type' => $request->has('crop_type') ? $request->get('crop_type') : 'fit',
            'crop_width' => $request->get('crop_width') != '' ? $request->get('crop_width') : null,
            'greyscale' => $request->has('greyscale'),
            'height' => $request->get('height') != '' ? $request->get('height') : null,
            'name' => $request->get('name'),
            'rotate' => $request->get('rotate') != '' ? $request->get('rotate') : 0,
            'upsizing' => $request->has('upsizing'),
            'width' => $request->get('width') != '' ? $request->get('width') : null
        ];

        if ($request->has('anchor'))
            list($data['anchor_h'], $data['anchor_v']) = explode('-', $request->get('anchor'));

        return $data;
    }

    private function validateImageStyleRequest($request, $style = null)
    {
        // validate input
        $rules = [
            'crop_height' => 'integer|min:1|required_with:crop_width',
            'crop_width' => 'integer|min:1|required_with:crop_height',
            'height' => 'integer|min:0',
            'name' => 'required|unique:image_styles',
            'rotate' => 'integer|min:0|max:360',
            'width' => 'integer|min:0'
        ];

        if ($request->isMethod('PATCH') && $style) {
            $rules['name'] = [
                'required',
                Rule::unique('image_styles')->ignore($style->id)
            ];
        }

        $this->validate($request, $rules);
    }

}