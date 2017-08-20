<?php

namespace Chronos\Content\Seeds;

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
            'name' => 'view_content_types',
            'label' => trans('chronos.content::permissions.View content types'),
            'order' => 10
        ]);

        Permission::create([
            'name' => 'add_content_types',
            'label' => trans('chronos.content::permissions.Add content types'),
            'order' => 10
        ]);

        Permission::create([
            'name' => 'edit_content_types',
            'label' => trans('chronos.content::permissions.Edit content types'),
            'order' => 10
        ]);

        Permission::create([
            'name' => 'edit_content_type_fieldsets',
            'label' => trans('chronos.content::permissions.Edit content type fieldsets'),
            'order' => 10
        ]);

        Permission::create([
            'name' => 'delete_content_types',
            'label' => trans('chronos.content::permissions.Delete content types'),
            'order' => 10
        ]);

        Permission::create([
            'name' => 'export_content_types',
            'label' => trans('chronos.content::permissions.Export content types'),
            'order' => 10
        ]);

        Permission::create([
            'name' => 'view_media',
            'label' => trans('chronos.content::permissions.View media'),
            'order' => 10
        ]);

        Permission::create([
            'name' => 'upload_media',
            'label' => trans('chronos.content::permissions.Upload media'),
            'order' => 10
        ]);

        Permission::create([
            'name' => 'delete_media',
            'label' => trans('chronos.content::permissions.Delete media'),
            'order' => 10
        ]);

        Permission::create([
            'name' => 'edit_languages',
            'label' => trans('chronos.content::permissions.Edit language settings'),
            'order' => 10
        ]);
    }

}