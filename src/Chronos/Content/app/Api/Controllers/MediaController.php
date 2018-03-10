<?php

namespace Chronos\Content\Api\Controllers;

use App\Http\Controllers\Controller;
use Chronos\Content\Models\Media;
use Chronos\Content\Services\ImageStyleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeExtensionGuesser;

class MediaController extends Controller
{

    public function index(Request $request)
    {
        $itemsPerPage = $request->has('perPage')
            ? $request->get('perPage') == 0 ? Media::noStyles()->count() : $request->get('perPage')
            : Config::get('content.media_items_per_page');

        $q = Media::noStyles();

        // filter
        if ($request->has('filters')) {
            $filters = $request->get('filters');

            if (isset($filters['search']) && $filters['search'] != '') {
                $q->where('filename', 'like', '%' . $filters['search'] . '%');

                if (strlen($filters['search']) > 2)
                    $q->orWhereRaw('LOWER(CONVERT(`data` USING utf8)) LIKE "%' . strtolower($filters['search']) . '%"');
            }

            if (isset($filters['imagesOnly']) && $filters['imagesOnly'] == 'true')
                $q->whereIn('type', Media::$image_types);
        }

        // sort
        $q->orderBy('created_at', 'DESC');

        // pagination
        $data = $q->paginate($itemsPerPage);

        return response()->json($data, 200);
    }

    public function destroy(Media $media)
    {
        $pathinfo = pathinfo($media->file);
        $path = parse_url($pathinfo['dirname'])['path'];
        $upload_path = public_path($path);

        foreach ($media->image_styles as $style) {
            if (file_exists($upload_path . '/' . $style->basename))
                unlink($upload_path . '/' . $style->basename);
        }

        if ($media->delete()) {
            if (file_exists($upload_path . '/' . $media->basename))
                unlink($upload_path . '/' . $media->basename);

            return response()->json([
                'alerts' => [
                    (object)[
                        'type' => 'success',
                        'title' => trans('chronos.content::alerts.Success.'),
                        'message' => trans('chronos.content::alerts.File successfully deleted.'),
                    ]
                ],
                'status' => 200
            ], 200);
        }
        else {
            return response()->json([
                'alerts' => [
                    (object)[
                        'type' => 'error',
                        'title' => trans('chronos.content::alerts.Error.'),
                        'message' => trans('chronos.content::alerts.File deletion was unsuccessful.'),
                    ]
                ],
                'status' => 500
            ], 500);
        }
    }

    public function destroy_bulk(Request $request)
    {
        $deleted_media_count = 0;

        if ($request->has('media')) {
            foreach ($request->get('media') as $media_id) {
                $media = Media::find($media_id);

                $pathinfo = pathinfo($media->file);
                $path = parse_url($pathinfo['dirname'])['path'];
                $upload_path = public_path($path);

                foreach ($media->image_styles as $style) {
                    if (file_exists($upload_path . '/' . $style->basename))
                        unlink($upload_path . '/' . $style->basename);
                }

                if ($media->delete()) {
                    if (file_exists($upload_path . '/' . $media->basename))
                        unlink($upload_path . '/' . $media->basename);

                    $deleted_media_count++;
                }
            }
        }

        if ($deleted_media_count > 0) {
            return response()->json([
                'alerts' => [
                    (object)[
                        'type' => 'success',
                        'title' => trans('chronos.content::alerts.Success.'),
                        'message' => trans_choice('chronos.content::alerts.:count items deleted.', $deleted_media_count, ['count' => $deleted_media_count])
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
                        'message' => trans_choice('chronos.content::alerts.:count items deleted.', $deleted_media_count, ['count' => $deleted_media_count])
                    ]
                ],
                'status' => 200
            ], 200);
        }
    }

    public function show(Media $media)
    {
        return response()->json($media, 200);
    }

    public function store(Request $request)
    {
        if ($request->has('files')) {
            $alerts_counter = [
                'error' => 0,
                'success' => 0
            ];
            $alerts = [];

            foreach ($request->get('files') as $key => $file) {
                list($mime, $data) = explode(';', $file);

                // guess extension
                $mime = str_replace('data:', '', $mime);
                $extensionGuesser = new MimeTypeExtensionGuesser();
                $extension = $extensionGuesser->guess($mime);

                $data = str_replace('base64,', '', $data);
                $data = base64_decode($data);

                // set up paths and filenames
                $paths = Config::get('content.upload_paths');
                $path = $paths[mt_rand(0, count($paths) - 1)]; // get a random upload path
                $upload_path = $path['upload_path'];
                if (!is_dir($upload_path))
                    mkdir($upload_path, 0755, true);
                $asset_path = $path['asset_path'];

                $filename = $request->has('fileNames') ? pathinfo($request->get('fileNames')[$key])['filename'] : str_random(12);
                $filename = transliterate(str_slug($filename));
                $basename = $filename . '.' . $extension;

                // make sure file names are unique
                $i = 1;
                $new_filename = $filename;
                while (file_exists($upload_path . '/' . $basename)) {
                    $new_filename = $filename . '-' . $i++;
                    $basename = $new_filename . '.' . $extension;
                }
                $filename = $new_filename;

                // upload file
                $filepath = $upload_path . '/' . $basename;
                if (file_put_contents($filepath, $data) === false) {
                    $alerts_counter['error']++;
                    $alerts[] = (object) [
                        'type' => 'error',
                        'title' => trans('chronos.content::alerts.Error.'),
                        'message' => trans('chronos.content::alerts.Error when uploading :file.', ['file' => $basename]),
                    ];

                    continue;
                }

                // create media model
                $file_url = $asset_path . '/' . $basename;
                $media = Media::create([
                    'file' => $file_url,
                    'filename' => $filename,
                    'basename' => $basename,
                    'type' => $extension,
                    'size' => filesize($filepath),
                ]);

                // if image, generate styles
                if (in_array($extension, Media::$image_types) && $extension != 'svg') {
                    // update media model
                    $image = Image::make($file);
                    $media->update([
                        'image_height' => $image->height(),
                        'image_width' => $image->width(),
                    ]);

                    // generate styles
                    ImageStyleService::make($asset_path, $filename, $extension, $media);
                }

                // set alert for this file
                $alerts_counter['success']++;
                $alerts[] = (object) [
                    'type' => 'success',
                    'title' => trans('chronos.content::alerts.Success.'),
                    'message' => trans('chronos.content::alerts.:file successfully uploaded.', ['file' => $basename]),
                ];
            }

            // if there were less than 3 files, show individual alerts
            if (($alerts_counter['error'] + $alerts_counter['success']) <= 3)
                return response()->json([
                    'alerts' => $alerts,
                    'status' => 200
                ], 200);
            // otherwise show aggregated alert
            else {
                // all were successful
                if ($alerts_counter['error'] == 0)
                    return response()->json([
                        'alerts' => [
                            (object) [
                                'type' => 'success',
                                'title' => trans('chronos.content::alerts.Success.'),
                                'message' => trans('chronos.content::alerts.:count files successfully uploaded.', ['count' => $alerts_counter['success']]),
                            ]
                        ],
                        'status' => 200
                    ], 200);
                // none were successfully
                else if ($alerts_counter['success'] == 0)
                    return response()->json([
                        'alerts' => [
                            (object) [
                                'type' => 'error',
                                'title' => trans('chronos.content::alerts.Error.'),
                                'message' => trans('chronos.content::alerts.:count files failed to upload.', ['count' => $alerts_counter['error']]),
                            ]
                        ],
                        'status' => 200
                    ], 200);
                // some were successful
                else
                    return response()->json([
                        'alerts' => [
                            (object) [
                                'type' => 'warning',
                                'title' => trans('chronos.content::alerts.Warning.'),
                                'message' => trans('chronos.content::alerts.:count_success file(s) successfully uploaded. :count_error file(s) failed to upload.', ['count_success' => $alerts_counter['success'], 'count_error' => $alerts_counter['error']]),
                            ]
                        ],
                        'status' => 200
                    ], 200);
            }
        }
        else {
            return response()->json([
                'alerts' => [
                    (object) [
                        'type' => 'error',
                        'title' => trans('chronos.content::alerts.Error.'),
                        'message' => trans('chronos.content::alerts.No files to upload.'),
                    ]
                ],
                'status' => 400
            ], 400);
        }
    }

    public function update(Request $request, Media $media)
    {
        $data = unserialize($media->data);
        $data['alt'] = $request->has('alt') ? $request->get('alt') : '';
        $data['title'] = $request->has('title') ? $request->get('title') : '';

        $media->data = serialize($data);
        $media->save();

        return response()->json([
            'alerts' => [
                (object) [
                    'type' => 'success',
                    'title' => trans('chronos.content::alerts.Success.'),
                    'message' => trans('chronos.content::alerts.Media successfully updated.'),
                ]
            ],
            'media' => $media,
            'status' => 200
        ], 200);
    }
}
