<?php

namespace Chronos\Scaffolding\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\Token;

class AccessToken extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['oauth_access_token_id', 'token'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;



    /**
     * A permission can be applied to roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function oauth_access_token()
    {
        return $this->belongsTo(Token::class);
    }

}