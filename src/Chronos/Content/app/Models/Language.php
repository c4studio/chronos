<?php

namespace Chronos\Content\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    /**
     * The attributes that are appended to the model's JSON form
     *
     * @var array
     */
    protected $appends = ['endpoints'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['code', 'name', 'status'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;



    /**
     * Add admin URLs to model.
     */
    public function getEndpointsAttribute()
    {
        $id = $this->attributes['id'];
        $endpoints['index'] = route('api.settings.languages');
        $endpoints['activate'] = route('api.settings.languages.activate', ['content' => $id]);
        $endpoints['deactivate'] = route('api.settings.languages.deactivate', ['content' => $id]);

        return $endpoints;
    }



    /**
     * Scope a query to only include active languages.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
    
}
