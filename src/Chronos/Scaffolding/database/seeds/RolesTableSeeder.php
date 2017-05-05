<?php

namespace Chronos\Scaffolding\Seeds;

use Chronos\Scaffolding\Models\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            'name' => 'root',
            'cloak' => 1
        ]);
    }
}
