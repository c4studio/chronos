<?php

namespace Chronos\Content\Models;

use Illuminate\Database\Eloquent\Model;

class ContentFieldData extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'content_fields_data';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['content_id', 'fieldset_repetition_key', 'field_id', 'field_repetition_key', 'value'];



    /**
     * Get associated content
     */
    public function content()
    {
        return $this->belongsTo('Chronos\Content\Models\Content', 'content_id');
    }

    /**
     * Get associated field
     */
    public function field()
    {
        return $this->belongsTo('Chronos\Content\Models\ContentField', 'field_id');
    }
}
