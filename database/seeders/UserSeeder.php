<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;


class UserSeeder extends Seeder
{
   /**
    * Run the database seeds.
    */
   public function run(): void
   {

      $people = [
         [ 'name' => 'Laura Kauweling', 'role' => 'organizer' ],
         [ 'name' => 'Eric Messol', 'role' => 'organizer' ],
         [ 'name' => 'Tanja Groot', 'role' => 'admin' ],
         [ 'name' => 'Max Klovinger', 'role' => 'organizer' ],
         [ 'name' => 'Klaas Muishoed' ],
         [ 'name' => 'Bob Tuinman' ],
         [ 'name' => 'Jan ter Klaas' ],
         [ 'name' => 'Peter Haven' ],
         [ 'name' => 'Dirk Woud' ],
         [ 'name' => 'Peter Molen' ],
         [ 'name' => 'Patrick Noot' ],
      ];
      
      User::create([
         'name' => 'Steven Yap',
         'email' => 'test@test.nl',
         'password' => bcrypt('testinglocally'),
         'role' => 'admin'
      ]);

      foreach($people as $index => $person){

         $parts = preg_split('/\s+/', trim($person['name']));
         $count = count($parts);

         $firstName = Str::lower($parts[0]);
         $lastName = ($count > 1) ? Str::lower( end($parts) ) : '';

         User::create([
            'name'      => $person['name'],
            'email'     => $firstName . '.' . $lastName . '@test.nl',
            'password'  => bcrypt('password' . $index),
            'role'      => $person['role'] ?? 'user',
         ]);

      }

   }
}
