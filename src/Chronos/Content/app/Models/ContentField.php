<?php

namespace Chronos\Content\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ContentField extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'content_fields';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['enable_alt', 'enable_title', 'entity_endpoints', 'entity_id', 'entity_model', 'step', 'values', 'valuesParsed'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['fieldset_id', 'name', 'machine_name', 'type', 'widget', 'default', 'repeatable', 'help_text', 'rules', 'data', 'order'];



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
     * Add enable alt tag to model.
     */
    public function getEnableAltAttribute()
    {
        if ($this->attributes['data'] == '' || $this->attributes['type'] != 'image')
            return null;

        $data = unserialize($this->attributes['data']);

        if (!isset($data['enable_alt']))
            return null;
        else {
            return $data['enable_alt'];
        }
    }

    /**
     * Add enable title tag to model.
     */
    public function getEnableTitleAttribute()
    {
        if ($this->attributes['data'] == '' || $this->attributes['type'] != 'image')
            return null;

        $data = unserialize($this->attributes['data']);

        if (!isset($data['enable_title']))
            return null;
        else {
            return $data['enable_title'];
        }
    }

    /**
     * Add entity id to model.
     */
    public function getEntityEndpointsAttribute()
    {
        $orig_id = $this->getEntityIdAttribute();
        $id = (int) $orig_id;
        $model_name = $this->getEntityModelAttribute();

        if (!is_null($model_name) && class_exists($model_name) && !is_null($orig_id) && is_integer($id)) {
            $model = $model_name::findOrFail($id);
            return $model->endpoints;
        }
        elseif (!is_null($model_name) && class_exists($model_name)) {
            $model = $model_name::first();
            return $model->endpoints;
        }
        else {
            return null;
        }
    }

    /**
     * Add entity id to model.
     */
    public function getEntityIdAttribute()
    {
        if ($this->attributes['data'] == '' || $this->attributes['type'] != 'entity')
            return null;

        $data = unserialize($this->attributes['data']);

        if (!isset($data['entity_type']))
            return null;
        else {
            $entity_type = explode(':', $data['entity_type']);

            return isset($entity_type[1]) ? $entity_type[1] : null;
        }
    }

    /**
     * Add entity model to model.
     */
    public function getEntityModelAttribute()
    {
        if ($this->attributes['data'] == '' || $this->attributes['type'] != 'entity')
            return null;

        $data = unserialize($this->attributes['data']);

        if (!isset($data['entity_type']))
            return null;
        else {
            $entity_type = explode(':', $data['entity_type']);

            return $entity_type[0];
        }
    }

    /**
     * Add step to model.
     */
    public function getStepAttribute()
    {
        if ($this->attributes['data'] == '' || $this->attributes['type'] != 'number')
            return null;

        $data = unserialize($this->attributes['data']);

        if (!isset($data['step']))
            return null;
        else {
            return $data['step'];
        }
    }

    /**
     * Add values to model.
     */
    public function getValuesAttribute()
    {
        if ($this->attributes['data'] == '' || $this->attributes['type'] != 'list')
            return null;

        $data = unserialize($this->attributes['data']);

        if (!isset($data['values']))
            return null;
        else {
            return $data['values'];
        }
    }

    /**
     * Add parsed values to model.
     */
    public function getValuesParsedAttribute()
    {
        if ($this->attributes['data'] == '')
            return null;

        $data = unserialize($this->attributes['data']);

        if (!isset($data['values']))
            return null;
        else {
            $values = preg_split("/\\r\\n|\\r|\\n/", $data['values']);
            $ret = [];

            foreach ($values as $item) {
                if (strpos($item, '=>')) {
                    $item = str_replace(' => ', '=>', $item);
                    list($key, $value) = explode('=>', $item);

                    $ret[$key] = $value;
                } else {
                    $ret[$item] = $item;
                }
            }

            return $ret;
        }
    }



    /**
     * Get associated fieldset
     */
    public function fieldset()
    {
        return $this->belongsTo('Chronos\Content\Models\ContentFieldset', 'fieldset_id');
    }
}
