<?php

namespace Chronos\Scaffolding\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /**
     * The attributes that are mass assignable
     *
     * @var array
     */
    protected $fillable = [
        'key', 'value'
    ];

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = null;

    /**
     * Indicates that the model should not be timestamped
     *
     * @var bool
     */
    public $timestamps = false;
}
