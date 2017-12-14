<?php

namespace Chronos\Content\Models;

use Chronos\Scaffolding\Models\ImageStyle;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'media';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['alt', 'endpoints', 'is_image', 'sizeFormatted', 'thumb', 'title'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['parent_id', 'file', 'filename', 'basename', 'type', 'size', 'image_height', 'image_width', 'image_style_id', 'data'];



    /**
     * Image file types
     *
     * @var array
     */
    public static $image_types = ['gif', 'jpeg', 'jpg', 'png', 'svg'];



    /**
     * Add alt tag to model.
     */
    public function getAltAttribute()
    {
        $data = unserialize($this->attributes['data']);

        return $data === false ? null : $data['alt'];
    }

    /**
     * Add admin URLs to model.
     */
    public function getEndpointsAttribute()
    {
        $id = $this->attributes['id'];
        $endpoints['index'] = route('api.content.media');
        $endpoints['destroy'] = route('api.content.media.destroy', ['media' => $id]);

        return $endpoints;
    }

    /**
     * Add is image attribute to model.
     */
    public function getIsImageAttribute()
    {
        return in_array($this->type, Media::$image_types);
    }

    /**
     * Add thumb to model.
     */
    public function getSizeFormattedAttribute()
    {
        if ($this->size >= (1000 ** 2))
            return number_format($this->size / (1000 ** 2), 2) . ' MB';
        else if ($this->size >= 1000)
            return number_format($this->size / 1000, 2) . ' KB';
        else if ($this->size > 1)
            return number_format($this->size) . ' bytes';
        else
            return number_format($this->size) . ' byte';
    }

    /**
     * Add styles to model.
     */
    public function getStylesAttribute()
    {
        if (!$this->isImage)
            return null;

        $styles = [];
        foreach ($this->image_styles as $style)
            $styles[$style->style->name] = $style->file;

        return $styles;
    }

    /**
     * Add thumb to model.
     */
    public function getThumbAttribute()
    {
        if (!$this->isImage)
            return null;

        $thumb_style = ImageStyle::where('name', 'Chronos Thumbnail')->first()->id;
        $thumb = $this->image_styles()->where('image_style_id', $thumb_style)->first();

        if ($thumb)
            return $thumb->file;
        else
            return $this->file;
    }

    /**
     * Add title tag to model.
     */
    public function getTitleAttribute()
    {
        $data = unserialize($this->attributes['data']);

        return $data === false ? null : $data['title'];
    }



    /**
     * Scope a query to exclude image styles.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNoStyles($query)
    {
        return $query->whereNull('parent_id');
    }



    /**
     * Get content to which this media is attached to.
     */
    public function attachedTo()
    {
        return $this->belongsToMany('\Chronos\Content\Models\Content', 'content_media', 'content_id', 'media_id');
    }

    /**
     * Get the styles for this media.
     */
    public function image_styles()
    {
        return $this->hasMany('\Chronos\Content\Models\Media', 'parent_id');
    }

    /**
     * Get styles's parent.
     */
    public function parent()
    {
        return $this->belongsTo('\Chronos\Content\Models\Media');
    }

    /**
     * Get the style type for this media.
     */
    public function style()
    {
        return $this->belongsTo('\Chronos\Scaffolding\Models\ImageStyle', 'image_style_id');
    }
}
