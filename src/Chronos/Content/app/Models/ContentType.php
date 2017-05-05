<?php

namespace Chronos\Content\Models;

use Illuminate\Database\Eloquent\Model;

class ContentType extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'content_types';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['admin_urls', 'endpoints'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'title_label', 'translatable', 'notes'];



    /**
     * Add admin URLs to model.
     */
    public function getAdminUrlsAttribute()
    {
        $id = $this->attributes['id'];
        $urls['edit'] = route('chronos.content.types.edit', ['type' => $id]);
        $urls['edit_fieldsets'] = route('chronos.content.types.fieldset', ['type' => $id]);

        return $urls;
    }

    /**
     * Add endpoints to model.
     */
    public function getEndpointsAttribute()
    {
        $id = $this->attributes['id'];
        $endpoints['index'] = route('api.content', ['type' => $id]);
        $endpoints['destroy'] = route('api.content.types.destroy', ['type' => $id]);
        $endpoints['update'] = route('api.content.types.update', ['type' => $id]);

        return $endpoints;
    }



    /**
     * Get content fieldsets.
     */
    public function fieldsets()
    {
        return $this->morphMany('\Chronos\Content\Models\ContentFieldset', 'parent');
    }

    /**
     * Get content items.
     */
    public function items()
    {
        return $this->hasMany('\Chronos\Content\Models\Content', 'type_id');
    }
}