<?php

namespace Chronos\Content\Http\Controllers;

use App\Http\Controllers\Controller;
use Chronos\Content\Models\ContentType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContentTypesController extends Controller
{

    public function index()
    {
        return view('chronos::content.types.index');
    }

    public function edit(ContentType $type)
    {
        return view('chronos::content.types.edit')->with([
            'type' => $type
        ]);
    }

    public function fieldsets(ContentType $type)
    {
        return view('chronos::content.fieldsets.edit')->with([
            'parent' => $type,
            'type' => $type
        ]);
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

        $type->update([
            'name' => $request->get('name'),
            'title_label' => $request->get('title_label'),
            'translatable' => $request->has('translatable'),
            'notes' => $request->get('notes')
        ]);

        // redirect
        return redirect()->route('chronos.content.types.edit', ['type' => $type])->with('alerts', [
            (object) [
                'type' => 'success',
                'title' => trans('chronos.content::alerts.Success.'),
                'message' => trans('chronos.content::alerts.Content type successfully updated.'),
            ]
        ]);
    }

}
