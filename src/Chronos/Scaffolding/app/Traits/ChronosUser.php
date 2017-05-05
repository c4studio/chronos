<?php

namespace Chronos\Scaffolding\Traits;

use Chronos\Scaffolding\Models\Permission;
use Chronos\Scaffolding\Models\Role;
use Chronos\Scaffolding\Notifications\ResetPasswordNotification;
use Laravel\Passport\HasApiTokens;

trait ChronosUser {

    use HasApiTokens;

    /**
     * Add admin URLs to model.
     */
    public function getEndpointsAttribute()
    {
        $id = $this->attributes['id'];

        $endpoints['index'] = route('api.users');

        return $endpoints;
    }

    /**
     * Determine if the user may perform the given permission.
     *
     * @param  Permission $permission
     * @return boolean
     */
    public function getNameAttribute()
    {
        if ($this->firstname && $this->lastname)
            return $this->firstname . ' ' . $this->lastname;
        else
            return $this->email;
    }



    /**
     * Determine if the user may perform the given permission.
     *
     * @param  Permission $permission
     * @return boolean
     */
    public function hasPermission($permission)
    {
        if ($this->hasRole('root'))
            return true;

        return $this->role->hasPermission($permission);
    }

    /**
     * Determine if the user  has one of the given permissions.
     *
     * @param $permissions
     * @return bool
     */
    public function hasOneOfPermissions($permissions)
    {
        if ($this->hasRole('root'))
            return true;

        $has = false;

        foreach ($permissions as $permission) {
            $has = $has || $this->hasPermission($permission);
        }

        return $has;
    }

    /**
     * Determine if the user has the given role.
     *
     * @param  mixed $roles
     * @return boolean
     */
    public function hasRole($role)
    {
        if (!$this->role)
            return false;

        return $this->role->name == $role;
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }



    /**
     * A user belongs to a role.
     *
     * @return mixed
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}