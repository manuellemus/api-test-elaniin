<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory(10)->create();
        \App\Models\User::factory()->create([
            'name'  => 'Manuel Lemus',
            'email' => 'manuellemuslemus23@gmail.com',
            'password' => bcrypt('12345678'),
            'username'  => 'Manuel Lemus',
            'telephone' => '70361911',
            'birthDate' => date('now'),  
        ]);
    }
}
