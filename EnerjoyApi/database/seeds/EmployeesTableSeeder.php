<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployeesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $password = bcrypt('secret');
        DB::table('employees')->insert([
            'first_name' => 'human',
            'last_name' => 'resources',
            'email' => 'humanresources@enerjoy.be',
            'password' => bcrypt('secret'),
            'salary' => 3000,
            'address_id' => 1,
            'job_id' => 1,
            'api_token' => 'testtoken'
        ]);
        $faker = Faker::create();
        foreach (range(1, 10) as $index) {
            DB::table('employees')->insert([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'email' => $faker->email,
                'password' => $password,
                'salary' => $faker->numberBetween($min = 1000, $max = 9000),
                'address_id' => $index,
                'job_id' => $index,
                'api_token' => Hash::make(Str::random(60))
            ]);
        }
    }
}
