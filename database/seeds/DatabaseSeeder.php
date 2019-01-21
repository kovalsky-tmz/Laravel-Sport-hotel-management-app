<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'email' => 'email@email.com',
            'password' => bcrypt('test'),// ALBO bcrypt
            'first_name' => 'test',
            'last_name' => 'test',
            'phone' => '555444333',
            'city' => 'Tak',
            'role' => 'admin',
            'confirmation_code' => 1
        ]);
    }
}
