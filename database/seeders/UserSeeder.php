<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        User::create([
            'name' => 'Steven Yap',
            'email' => 'test@test.nl',
            'password' => bcrypt('testinglocally'),
            'role' => 'master'
        ]);

        User::create([
            'name' => 'Laura Kauweling',
            'email' => 'laurat@test.nl',
            'password' => bcrypt('password1'),
            'role' => 'organizer'
        ]);

        User::create([
            'name' => 'Eric Messol',
            'email' => 'eric@test.nl',
            'password' => bcrypt('password2'),
            'role' => 'organizer'
        ]);
    }
}
