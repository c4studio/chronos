<?php

namespace Chronos\Scaffolding\Seeds;

use Chronos\Scaffolding\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create([
            'name' => 'view_dashboard',
            'label' => trans('chronos.scaffolding::permissions.View dashboard'),
            'order' => 10
        ]);

        Permission::create([
            'name' => 'edit_settings',
            'label' => trans('chronos.scaffolding::permissions.Edit settings'),
            'order' => 10
        ]);

        Permission::create([
            'name' => 'edit_access_tokens',
            'label' => trans('chronos.scaffolding::permissions.Edit access tokens'),
            'order' => 10
        ]);

        Permission::create([
            'name' => 'delete_access_tokens',
            'label' => trans('chronos.scaffolding::permissions.Delete access tokens'),
            'order' => 10
        ]);

        Permission::create([
            'name' => 'view_image_styles',
            'label' => trans('chronos.scaffolding::permissions.View image styles'),
            'order' => 10
        ]);

        Permission::create([
            'name' => 'add_image_styles',
            'label' => trans('chronos.scaffolding::permissions.Add image styles'),
            'order' => 10
        ]);

        Permission::create([
            'name' => 'edit_image_styles',
            'label' => trans('chronos.scaffolding::permissions.Edit images styles'),
            'order' => 10
        ]);

        Permission::create([
            'name' => 'delete_image_styles',
            'label' => trans('chronos.scaffolding::permissions.Delete image styles'),
            'order' => 10
        ]);

        Permission::create([
            'name' => 'view_roles',
            'label' => trans('chronos.scaffolding::permissions.View roles'),
            'order' => 10
        ]);

        Permission::create([
            'name' => 'add_roles',
            'label' => trans('chronos.scaffolding::permissions.Add roles'),
            'order' => 10
        ]);

        Permission::create([
            'name' => 'edit_roles',
            'label' => trans('chronos.scaffolding::permissions.Edit roles'),
            'order' => 10
        ]);

        Permission::create([
            'name' => 'delete_roles',
            'label' => trans('chronos.scaffolding::permissions.Delete roles'),
            'order' => 10
        ]);

        Permission::create([
            'name' => 'edit_permissions',
            'label' => trans('chronos.scaffolding::permissions.Edit permissions'),
            'order' => 10
        ]);
    }

}