<?php
/**
 * Replacement template for [media] shortcode
 *
 * \Chronos\Content\Models\Media $media
 * string $alt
 * string $title
 * string $style
 */
?>
<a href="{{ $url }}"@if ($target) target="{{ $target }}" @endif>{{ $text }}</a>