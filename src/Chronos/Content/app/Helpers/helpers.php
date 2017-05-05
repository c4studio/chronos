<?php

use Chronos\Content\Services\WysiwygService;

if ( ! function_exists('filter_wysiwyg'))
{
    function filter_wysiwyg($text)
    {
        return WysiwygService::filter_wysiwyg($text);
    }
}