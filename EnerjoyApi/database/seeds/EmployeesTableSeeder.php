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
        DB::table('employees')->insert([
            'first_name' => 'admin',
            'last_name' => 'admin',
            'email' => 'admin@enerjoy.be',
            'password' => bcrypt('secret'),
            'salary' => 9000,
            'phone' => 'xxxxxxxxxx',
            'ssn' => 'xxxxxxxxxx',
            'birthdate' => '1999-09-02',
            'address_id' => 1,
            'job_id' => 1
        ]);
        DB::table('employees')->insert([
            'first_name' => 'human',
            'last_name' => 'resources',
            'email' => 'humanresources@enerjoy.be',
            'password' => bcrypt('secret'),
            'salary' => 9000,
            'phone' => 'xxxxxxxxxx',
            'ssn' => 'xxxxxxxxxx',
            'birthdate' => '1999-09-02',
            'address_id' => 1,
            'job_id' => 2
        ]);
        $faker = Faker::create();
        foreach (range(3, 12) as $index) {
            DB::table('employees')->insert([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'email' => $faker->email,
                'password' => bcrypt('secret'),
                'salary' => $faker->numberBetween($min = 1000, $max = 9000),
                'phone' => $faker->phoneNumber,
                'ssn' => $faker->numerify("###-###-###-##"),
                'birthdate' => $faker->date,
                'address_id' => $index,
                'job_id' => $index
            ]);
        }
    }
}
