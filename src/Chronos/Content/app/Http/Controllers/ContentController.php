<?php

namespace Chronos\Content\Http\Controllers;

use App\Http\Controllers\Controller;

use Chronos\Content\Models\Content;
use Chronos\Content\Models\ContentType;
use Illuminate\Support\Facades\Auth;

class ContentController extends Controller
{

    public function index(ContentType $type)
    {
        if (!Auth::user()->hasPermission('view_content_type_' . $type->id)) {
            abort(403);
        }

        return view('chronos::content.manage.index')->with('type', $type);
    }

    public function create(ContentType $type)
    {
        if (!Auth::user()->hasPermission('add_content_type_' . $type->id)) {
            abort(403);
        }

        return view('chronos::content.manage.create')->with('type', $type);
    }

    public function edit(ContentType $type, Content $content)
    {
        if (!Auth::user()->hasPermission('edit_content_type_' . $type->id)) {
            abort(403);
        }

        return view('chronos::content.manage.edit')->with([
            'content' => $content,
            'type' => $type
        ]);
    }

    public function fieldsets(ContentType $type, Content $content)
    {
        return view('chronos::content.fieldsets.edit')->with([
            'parent' => $content,
            'type' => $type
        ]);
    }

}
