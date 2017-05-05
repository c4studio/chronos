<?php

namespace Chronos\Content\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ContentFieldset extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'content_fieldsets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['parent_id', 'parent_type', 'name', 'machine_name', 'description', 'repeatable', 'order'];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['fields'];



    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        /**
         * Default ordering.
         * Remove from query with withoutGlobalScope('order')
         */
        static::addGlobalScope('order', function(Builder $builder) {
            $builder->orderBy('order', 'ASC')->orderBy('created_at', 'DESC');
        });
    }



    /**
     * Get associated fields
     */
    public function fields()
    {
        return $this->hasMany('Chronos\Content\Models\ContentField', 'fieldset_id');
    }
}
