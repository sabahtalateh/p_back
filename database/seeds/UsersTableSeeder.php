<?php

use FakerRestaurant\Provider\de_AT\Restaurant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();
        $faker->addProvider(new Restaurant($faker));

        DB::table('tokens')->delete();
        DB::table('users')->delete();

        DB::table('users')->insert([
            'email' => 'user1@mail.com',
            'password' => Hash::make('password')
        ]);

        DB::table('users')->insert([
            'email' => 'user2@mail.com',
            'password' => Hash::make('password')
        ]);

        DB::table('users')->insert([
            'email' => 'user3@mail.com',
            'password' => Hash::make('password')
        ]);

        DB::table('users')->insert([
            'email' => 'user4@mail.com',
            'password' => Hash::make('password')
        ]);
    }
}
