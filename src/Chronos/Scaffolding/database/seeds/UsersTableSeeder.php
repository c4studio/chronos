<?php

namespace Chronos\Scaffolding\Seeds;

use App\Models\User;
use Chronos\Scaffolding\Models\Role;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'email' => 'root@c4studio.ro',
            'password' => bcrypt('eidg0awy'),
            'firstname' => 'Super',
            'lastname' => 'Admin'
        ]);
        $user->role()->associate(Role::where('name', 'root')->first());
        $user->save();
    }
}
