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
            'name'  => 'admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('12345678'),
            'username'  => 'administrador',
            'telephone' => '70361911',
            'birthDate' => date('now'),  
        ]);
    }
}
