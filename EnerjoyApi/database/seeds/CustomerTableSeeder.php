<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class CustomerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        foreach (range(1, 10) as $index) {
            DB::table('customers')->insert([
                'firstname' => $faker->name,
                'lastname' => $faker->name,
                'email' => $faker->email,
                'password' => password_hash('secret',PASSWORD_DEFAULT),
                'addressId' => $index
            ]);
        }
    }
}
