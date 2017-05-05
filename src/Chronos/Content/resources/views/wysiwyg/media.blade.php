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
<div class="media">
    @if ($style && isset($media->styles[$style]))
        <img class="img-responsive" src="{{ $media->styles[$style] }}" alt="{{ $alt }}" title="{{ $title }}" />
    @else
        <img class="img-responsive" src="{{ $media->file }}" alt="{{ $alt }}" title="{{ $title }}" />
    @endif
</div>