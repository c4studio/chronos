<?php

namespace Chronos\Scaffolding\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Role extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'cloak'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['endpoints'];



    /**
     * Add admin URLs to model.
     */
    public function getEndpointsAttribute()
    {
        $id = $this->attributes['id'];

        $endpoints['index'] = route('api.users.roles');
        $endpoints['users'] = route('api.users.roles.users', ['type' => $id]);
        $endpoints['update'] = route('api.users.roles.update', ['type' => $id]);
        $endpoints['destroy'] = route('api.users.roles.destroy', ['type' => $id]);

        return $endpoints;
    }



    /**
     * Scope a query to only include uncloaked roles.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUncloaked($query)
    {
        return $query->where('cloak', '!=', 1);
    }



    /**
     * Check if role has permission.
     *
     * @param $permission
     * @return bool
     */
    public function hasPermission($permission)
    {
        return $this->permissions->contains('name', $permission);
    }



    /**
     * Get permissions attached to this role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    /**
     * Get users with this role.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

}