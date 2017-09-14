<?php

namespace Chronos\Scaffolding\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ImageStyle extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'height', 'width', 'crop_height', 'crop_width', 'crop_type', 'anchor_h', 'anchor_v', 'upsizing', 'rotate', 'greyscale', 'cloak'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['admin_urls', 'endpoints'];



    /**
     * Add admin URLs to model.
     */
    public function getAdminUrlsAttribute()
    {
        $id = $this->attributes['id'];
        $urls['edit'] = route('chronos.settings.image_styles.edit', ['style' => $id]);

        return $urls;
    }

    /**
     * Add admin URLs to model.
     */
    public function getEndpointsAttribute()
    {
        $id = $this->attributes['id'];

        $endpoints['index'] = route('api.settings.image_styles');
        $endpoints['update'] = route('api.settings.image_styles.update', ['style' => $id]);
        $endpoints['destroy'] = route('api.settings.image_styles.destroy', ['style' => $id]);
        $endpoints['destroy_styles'] = route('api.settings.image_styles.destroy_styles', ['style' => $id]);

        return $endpoints;
    }



    /**
     * Scope a query to only include uncloaked styles.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUncloaked($query)
    {
        return $query->where('cloak', '!=', 1);
    }
}