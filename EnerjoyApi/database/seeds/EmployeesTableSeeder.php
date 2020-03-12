<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class EmployeesTableSeeder extends Seeder
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
            DB::table('employees')->insert([
                'first_name' => $faker->name,
                'last_name' => $faker->name,
                'email' => $faker->email,
                'password' => bcrypt('secret'),
                'salary' => $faker->numberBetween($min = 1000, $max = 9000),
                'address_id' => $index,
                'job_id' => $index++
            ]);
        }
    }
}
