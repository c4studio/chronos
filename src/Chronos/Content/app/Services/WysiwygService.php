<?php

namespace Chronos\Content\Services;

class WysiwygService
{

    public static function filter_wysiwyg($text)
    {
        // normalize newlines
        $text = preg_replace('~\r\n?~', "\n", $text);

        // wrap in paragraphs
        $text = '<p>' . $text . '</p>';

        // handle unordered lists
        $ul_tpl = \Illuminate\Support\Facades\View::make('chronos::wysiwyg.ul')->with('content', '$1')->render();
        $text = preg_replace('/^\* (.*)<\/p>/m', '<liul>$1</liul></p>', $text);
        $text = preg_replace('/^\* (.*)(\n|$)/m', '<liul>$1</liul>', $text);
        $text = preg_replace('/<p>\* (.*)(\n|$)/m', '<p><liul>$1</liul>', $text);
        $text = preg_replace('/(<liul>.*<\/liul>)(\n)*/', '</p>' . $ul_tpl . '<p>', $text);
        $text = str_replace('<liul>', '<li>', $text);
        $text = str_replace('</liul>', '</li>', $text);

        // handle ordered lists with char
        $olchar_tpl = \Illuminate\Support\Facades\View::make('chronos::wysiwyg.olchar')->with('content', '$1')->render();
        $text = preg_replace('/^@ (.*)<\/p>/m', '<liolchar>$1</liolchar></p>', $text);
        $text = preg_replace('/^@ (.*)(\n|$)/m', '<liolchar>$1</liolchar>', $text);
        $text = preg_replace('/<p>@ (.*)(\n|$)/m', '<p><liolchar>$1</liolchar>', $text);
        $text = preg_replace('/(<liolchar>.*<\/liolchar>)(\n)*/', '</p>' . $olchar_tpl . '<p>', $text);
        $text = str_replace('<liolchar>', '<li>', $text);
        $text = str_replace('</liolchar>', '</li>', $text);

        // handle order lists with numbers
        $olno_tpl = \Illuminate\Support\Facades\View::make('chronos::wysiwyg.olno')->with('content', '$1')->render();
        $text = preg_replace('/^# (.*)<\/p>/m', '<liolno>$1</liolno></p>', $text);
        $text = preg_replace('/^# (.*)(\n|$)/m', '<liolno>$1</liolno>', $text);
        $text = preg_replace('/<p># (.*)(\n|$)/m', '<p><liolno>$1</liolno>', $text);
        $text = preg_replace('/(<liolno>.*<\/liolno>)(\n)*/', '</p>' . $olno_tpl . '<p>', $text);
        $text = str_replace('<liolno>', '<li>', $text);
        $text = str_replace('</liolno>', '</li>', $text);

        // handle tables
        if (preg_match_all('/\|(.*)\|(\n|$)/', $text, $rows)) {
            foreach ($rows[0] as $row) {
                $replace = trim($row);
                if (preg_match('/\|\|(.+)\|\|/', $row)) {
                    $replace = '<th>' . trim($replace, '||') . '</th>';
                    $replace = str_replace('||', '</th><th>', $replace);
                } else {
                    $replace = '<td>' . trim($replace, '|') . '</td>';
                    $replace = str_replace('|', '</td><td>', $replace);
                }
                $replace = '<tr>' . $replace . '</tr>';
                $text = str_replace($row, $replace, $text);
            }
        }
        $table_tpl = \Illuminate\Support\Facades\View::make('chronos::wysiwyg.table')->with('content', '$1')->render();
        $text = preg_replace('/(<tr>.*<\/tr>)(\n)*/', '</p>' . $table_tpl . '<p>', $text);

        // handle code
        $text = preg_replace('/(^|\s|[`\-_\+\*~\^>])`([^\s](?:.*?)[^\s])`(\s|[`\-_\+\*~\^<]|$)/', '$1<code>$2</code>$3', $text);

        // handle del
        $text = preg_replace('/(^|\s|[`\-_\+\*~\^>])-([^\s](?:.*?)[^\s])-(\s|[`\-_\+\*~\^<]|$)/', '$1<del>$2</del>$3', $text);

        // handle em
        $text = preg_replace('/(^|\s|[`\-_\+\*~\^>])_([^\s](?:.*?)[^\s])_(\s|[`\-_\+\*~\^<]|$)/', '$1<em>$2</em>$3', $text);

        // handle ins
        $text = preg_replace('/(^|\s|[`\-_\+\*~\^>])\+([^\s](?:.*?)[^\s])\+(\s|[`\-_\+\*~\^<]|$)/', '$1<ins>$2</ins>$3', $text);

        // handle strong
        $text = preg_replace('/(^|\s|[`\-_\+\*~\^>])\*([^\s](?:.*?)[^\s])\*(\s|[`\-_\+\*~\^<]|$)/', '$1<strong>$2</strong>$3', $text);

        // handle sub
        $text = preg_replace('/(^|\s|[`\-_\+\*~\^>])~([^\s](?:.*?)[^\s])~(\s|[`\-_\+\*~\^<]|$)/', '$1<sub>$2</sub>$3', $text);

        // handle sup
        $text = preg_replace('/(^|\s|[`\-_\+\*~\^>])\^([^\s](?:.*?)[^\s])\^(\s|[`\-_\+\*~\^<]|$)/', '$1<sup>$2</sup>$3', $text);

        // change double newlines in paragraphs
        $text = preg_replace('/(^|[^\n])\n{2}(?!\n)/', '$1</p><p>', $text);
        // change single newlines in line breaks
        $text = preg_replace('/(^|[^\n])\n(?!\n)/', '$1<br />', $text);

        // handle alignments
        $align_left_tpl = \Illuminate\Support\Facades\View::make('chronos::wysiwyg.align_left')->with('content', '$1')->render();
        $align_center_tpl = \Illuminate\Support\Facades\View::make('chronos::wysiwyg.align_center')->with('content', '$1')->render();
        $align_right_tpl = \Illuminate\Support\Facades\View::make('chronos::wysiwyg.align_right')->with('content', '$1')->render();
        $align_justify_tpl = \Illuminate\Support\Facades\View::make('chronos::wysiwyg.align_justify')->with('content', '$1')->render();
        static::context_aware_replace_1('\{left}(.*){left}', $align_left_tpl, $text);
        static::context_aware_replace_1('\{center}(.*){center}', $align_center_tpl, $text);
        static::context_aware_replace_1('\{right}(.*){right}', $align_right_tpl, $text);
        static::context_aware_replace_1('\{justify}(.*){justify}', $align_justify_tpl, $text);

        // handle gallery
        $gallery_tpl = \Illuminate\Support\Facades\View::make('chronos::wysiwyg.gallery');
        $matches = [];
        while (static::context_aware_match('\[gallery(.*?)\]', $text, $matches)) {
            $attr_matches = static::extract_attributes($matches[1]);

            // skip if attributes don't match up
            if (count($attr_matches[1]) != count($attr_matches[2])) {
                static::context_aware_replace_2('\[gallery(.*?)\]', $matches[0], '', $text);
                continue;
            }

            foreach ($attr_matches[1] as $key => $attr)
                $attrs[$attr] = $attr_matches[2][$key];

            // skip if id is not set
            if (!isset($attrs['id'])) {
                static::context_aware_replace_2('\[gallery(.*?)\]', $matches[0], '', $text);
                continue;
            }

            // skip if gallery model not found
            if (!$gallery = \Chronos\Content\Models\Content::find($attrs['id'])) {
                static::context_aware_replace_2('\[gallery(.*?)\]', $matches[0], '', $text);
                continue;
            }

            // skip if not a gallery
            if ($gallery->type->name != 'Gallery') {
                static::context_aware_replace_2('\[gallery(.*?)\]', $matches[0], '', $text);
                continue;
            }

            $gallery = $gallery->images;
            foreach ($gallery as $key => &$image) {
                $image = (object) $image->file;
                $image->media = \Chronos\Content\Models\Media::find($image->media_id);
                unset($image->media_id);
            }

            // replace
            $replace_tpl = $gallery_tpl->with([
                'gallery' => $gallery
            ])->render();
            static::context_aware_replace_2('\[gallery(.*?)\]', $matches[0], $replace_tpl, $text);
        }

        // handle link
        $link_tpl = \Illuminate\Support\Facades\View::make('chronos::wysiwyg.link');
        while (preg_match('/\[link(.*?)\]/', $text, $matches)) {
            $attr_matches = static::extract_attributes($matches[1]);

            // skip if attributes don't match up
            if (count($attr_matches[1]) != count($attr_matches[2])) {
                $text = preg_replace('/\[link(.*?)\]/', '', $text);
                continue;
            }

            foreach ($attr_matches[1] as $key => $attr)
                $attrs[$attr] = $attr_matches[2][$key];


            // skip if text or url is not set
            if (!isset($attrs['text']) || !isset($attrs['url'])) {
                $text = preg_replace('/\[link(.*?)\]/', '', $text);
                continue;
            }

            // replace
            $replace_tpl = $link_tpl->with([
                'target' => isset($attrs['target']) ? $attrs['target'] : null,
                'text' => $attrs['text'],
                'url' => $attrs['url']
            ])->render();
            $text = preg_replace($matches[0], $replace_tpl, $text);
        }

        // handle media
        $media_tpl = \Illuminate\Support\Facades\View::make('chronos::wysiwyg.media');
        $matches = [];
        while (static::context_aware_match('\[media(.*?)\]', $text, $matches)) {
            $attr_matches = static::extract_attributes($matches[1]);

            // skip if attributes don't match up
            if (count($attr_matches[1]) != count($attr_matches[2])) {
                static::context_aware_replace_2('\[media(.*?)\]', $matches[0], '', $text);
                continue;
            }

            foreach ($attr_matches[1] as $key => $attr) {
                $attrs[$attr] = $attr_matches[2][$key];
            }

            // skip if id is not set
            if (!isset($attrs['id'])) {
                static::context_aware_replace_2('\[media(.*?)\]', $matches[0], '', $text);
                continue;
            }

            // skip if media model not found
            if (!$media = \Chronos\Content\Models\Media::find($attrs['id'])) {
                static::context_aware_replace_2('\[media(.*?)\]', $matches[0], '', $text);
                continue;
            }

            // replace
            $replace_tpl = $media_tpl->with([
                'media' => $media,
                'alt' => isset($attrs['alt']) ? $attrs['alt'] : '',
                'title' => isset($attrs['title']) ? $attrs['title'] : '',
                'style' => isset($attrs['style']) ? $attrs['style'] : config('chronos.default_image_style')
            ])->render();
            static::context_aware_replace_2('\[media(.*?)\]', $matches[0], $replace_tpl, $text);
        }

        // handle quotes
        $quote_tpl = \Illuminate\Support\Facades\View::make('chronos::wysiwyg.quote')->with('content', '$1')->render();
        static::context_aware_replace_1('\{quote}(.*){quote}', $quote_tpl, $text);

        // handle youtube
        $youtube_tpl = \Illuminate\Support\Facades\View::make('chronos::wysiwyg.youtube');
        $matches = [];
        while (static::context_aware_match('\[youtube(.*?)\]', $text, $matches)) {
            $attr_matches = static::extract_attributes($matches[1]);

            // skip if attributes don't match up
            if (count($attr_matches[1]) != count($attr_matches[2])) {
                static::context_aware_replace_2('\[youtube(.*?)\]', $matches[0], '', $text);
                continue;
            }

            foreach ($attr_matches[1] as $key => $attr)
                $attrs[$attr] = $attr_matches[2][$key];

            // skip vid is not set
            if (!isset($attrs['vid'])) {
                static::context_aware_replace_2('\[youtube(.*?)\]', $matches[0], '', $text);
                continue;
            }

            // replace
            $replace_tpl = $youtube_tpl->with([
                'vid' => $attrs['vid']
            ])->render();
            static::context_aware_replace_2('\[youtube(.*?)\]', $matches[0], $replace_tpl, $text);
        }

        // remove empty paragraphs
        $text = str_replace('<p></p>', '', $text);
        $text = str_replace('<p><br /></p>', '', $text);

        // return filtered text
        return $text;
    }


    private static function context_aware_match($regex, $text, &$matches) {
        return preg_match('/<br \/>' . $regex . '<br \/>/', $text, $matches) || preg_match('/<br \/>' . $regex . '<\/p>/', $text, $matches) || preg_match('/<p>' . $regex . '<br \/>/', $text, $matches) || preg_match('/<p>' . $regex . '<\/p>/', $text, $matches);
    }

    private static function context_aware_replace_1($regex, $replace, &$text) {
        $text = preg_replace('/<p>' . $regex . '<\/p>/', $replace, $text);
        $text = preg_replace('/<br \/>' . $regex . '<\/p>/', '</p>' . $replace, $text);
        $text = preg_replace('/<p>' . $regex . '<br \/>/', $replace . '<p>', $text);
        $text = preg_replace('/<br \/>' . $regex . '<br \/>/', '</p>' . $replace . '<p>', $text);
    }

    private static function context_aware_replace_2($regex, $search, $replace, &$text) {
        if (preg_match('/<p>' . $regex . '<\/p>/', $search))
            $text = str_replace($search, $replace, $text);
        if (preg_match('/<br \/>' . $regex . '<\/p>/', $search))
            $text = str_replace($search, '</p>' . $replace, $text);
        if (preg_match('/<p>' . $regex . '<br \/>/', $search))
            $text = str_replace($search, $replace . '<p>', $text);
        if (preg_match('/<br \/>' . $regex . '<br \/>/', $search))
            $text = str_replace($search, '</p>' . $replace . '<p>', $text);
    }

    private static function extract_attributes($search) {
        if (preg_match_all('/((?:(?!\s|=).)*)\s*?=\s*?[\"\\\']?((?:(?<=\")(?:(?<=\\\\)\"|[^\"])*|(?<=\\\')(?:(?<=\\\\)\\\'|[^\\\'])*)|(?:(?!\"|\\\')(?:(?!\/>|>|\s).)+))/', $search, $attrs))
            return $attrs;
        else
            return [];
    }

}