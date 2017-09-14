<?php

namespace Chronos\Scaffolding\Api\Controllers\Settings;

use App\Http\Controllers\Controller;
use Chronos\Content\Models\Media;
use Chronos\Content\Services\ImageStyleService;
use Chronos\Scaffolding\Models\ImageStyle;
use Chronos\Scaffolding\Traits\ImageStyleManagement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class ImageStylesController extends Controller
{
    use ImageStyleManagement;

    public function index(Request $request)
    {
        $itemsPerPage = Config::get('chronos.items_per_page');

        $q = ImageStyle::uncloaked();

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
                                'title' => trans('chronos.scaffolding::alerts.Error.'),
                                'message' => trans('chronos.scaffolding::alerts.Invalid sortBy argument: :arg.', ['arg' => $sortBy]),
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

    public function destroy(ImageStyle $style)
    {
        if ($style->delete())
            return response()->json([
                'alerts' => [
                    (object) [
                        'type' => 'success',
                        'title' => trans('chronos.scaffolding::alerts.Success.'),
                        'message' => trans('chronos.scaffolding::alerts.Image style deletion was successful.'),
                    ]
                ],
                'status' => 200
            ], 200);
        else
            return response()->json([
                'alerts' => [
                    (object) [
                        'type' => 'error',
                        'title' => trans('chronos.scaffolding::alerts.Error.'),
                        'message' => trans('chronos.scaffolding::alerts.Image style deletion was unsuccessful.'),
                    ]
                ],
                'status' => 500
            ], 500);
    }

    public function destroy_styles(ImageStyle $style)
    {
        $media = Media::where('image_style_id', $style->id)->get();
        $deleted_styles_count = 0;

        foreach ($media as $file) {
            $pathinfo = pathinfo($file->file);
            $path = parse_url($pathinfo['dirname'])['path'];
            $upload_path = public_path($path);

            if (file_exists($upload_path . '/' . $file->basename)) {
                unlink($upload_path . '/' . $file->basename);
                $deleted_styles_count++;
            }
        }

        if ($deleted_styles_count > 0) {
            return response()->json([
                'alerts' => [
                    (object)[
                        'type' => 'success',
                        'title' => trans('chronos.scaffolding::alerts.Success.'),
                        'message' => trans_choice('chronos.scaffolding::alerts.:count images styles deleted.', $deleted_styles_count, ['count' => $deleted_styles_count])
                    ]
                ],
                'status' => 200
            ], 200);
        } else {
            return response()->json([
                'alerts' => [
                    (object)[
                        'type' => 'warning',
                        'title' => trans('chronos.scaffolding::alerts.Warning.'),
                        'message' => trans_choice('chronos.scaffolding::alerts.:count image styles deleted.', $deleted_styles_count, ['count' => $deleted_styles_count])
                    ]
                ],
                'status' => 200
            ], 200);
        }
    }

    public function show(ImageStyle $style)
    {
        return response()->json($style, 200);
    }

    public function store(Request $request)
    {
        // validate input
        $this->validateImageStyleRequest($request);


        // create image style
        $data = $this->getImageStyleData($request);
        $style = ImageStyle::create($data);

        return response()->json([
            'alerts' => [
                (object) [
                    'type' => 'success',
                    'title' => trans('chronos.scaffolding::alerts.Success.'),
                    'message' => trans('chronos.scaffolding::alerts.Image style successfully created.'),
                ]
            ],
            'style' => $style,
            'status' => 200
        ], 200);
    }

    public function update(Request $request, ImageStyle $style)
    {
        // validate input
        $this->validateImageStyleRequest($request, $style);

        // update image style
        $data = $this->getImageStyleData($request);
        $style->update($data);

        return response()->json([
            'alerts' => [
                (object) [
                    'type' => 'success',
                    'title' => trans('chronos.scaffolding::alerts.Success.'),
                    'message' => trans('chronos.scaffolding::alerts.Image style successfully updated.'),
                ]
            ],
            'style' => $style,
            'status' => 200
        ], 200);
    }

}
