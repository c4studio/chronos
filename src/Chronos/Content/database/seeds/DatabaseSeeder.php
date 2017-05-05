<?php

namespace Chronos\Content\Seeds;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(GallerySeeder::class);
        $this->call(PermissionsTableSeeder::class);
    }

}