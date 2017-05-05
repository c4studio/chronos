<?php

namespace Chronos\Content\Services;

use Chronos\Content\Models\Media;
use Chronos\Scaffolding\Models\ImageStyle;
use Intervention\Image\Facades\Image;

class ImageStyleService
{

    public static function generate($file, $upload_path, $asset_path, $filename, $extension, $parent, $style = null)
    {
        $q = ImageStyle::query()->withoutGlobalScope('uncloaked');
        if ($style)
            $q->where('id', $style->id);
        $image_styles = $q->get();

        foreach ($image_styles as $image_style) {
            $image = Image::make($file);

            // resize
            if ($image_style->height || $image_style->width) {
                $image->resize($image_style->width, $image_style->height, function ($constraint) use ($image_style) {
                    $constraint->aspectRatio();

                    if (!$image_style->upsizing)
                        $constraint->upsize();
                });
            }

            // rotate
            if ($image_style->rotate != 0)
                $image->rotate($image_style->rotate);

            // crop
            if ($image_style->crop_height && $image_style->crop_width) {
                if ($image_style->crop_type == 'fit') {
                    $image->fit($image_style->crop_width, $image_style->crop_height, function ($constraint) use ($image_style) {
                        if (!$image_style->upsizing)
                            $constraint->upsize();
                    });
                } else {
                    $crop_x = null;
                    $crop_y = null;

                    switch ($image_style->anchor_h) {
                        case 'left':
                            $crop_x = 0;
                            break;
                        case 'center':
                            $crop_x = $image->width() / 2 - $image_style->crop_width / 2;
                            break;
                        case 'right':
                            $crop_x = $image->width() - $image_style->crop_width;
                            break;
                    }

                    switch ($image_style->anchor_h) {
                        case 'top':
                            $crop_y = 0;
                            break;
                        case 'middle':
                            $crop_y = $image->height() / 2 - $image_style->crop_height / 2;
                            break;
                        case 'bottom':
                            $crop_y = $image->height() - $image_style->crop_height;
                            break;
                    }

                    $image->crop($image_style->crop_width, $image_style->crop_height, $crop_x, $crop_y);
                }
            }

            // greyscale
            if ($image_style->greyscale)
                $image->greyscale();

            // save
            $style_filename = $filename . '-' . str_slug($image_style->name);
            $style_basename = $style_filename . '.' . $extension;
            $filepath = $upload_path . '/' . $style_basename;
            $image->save($filepath, 80);

            // create media model
            $file_url = $asset_path . '/' . $style_basename;
            Media::create([
                'parent_id' => $parent->id,
                'file' => $file_url,
                'filename' => $style_filename,
                'basename' => $style_basename,
                'type' => $extension,
                'size' => $image->filesize(),
                'image_height' => $image->height(),
                'image_width' => $image->width(),
                'image_style_id' => $image_style->id
            ]);
        }
    }

}