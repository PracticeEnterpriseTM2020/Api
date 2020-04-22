<?php

use Carbon\Traits\Date;
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
            'job_id' => 1,
            'created_at' => Date("Y/m/d")
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
            'job_id' => 2,
            'created_at' => Date("Y/m/d")
        ]);
        DB::table('employees')->insert([
            'first_name' => 'user',
            'last_name' => 'user',
            'email' => 'user@enerjoy.be',
            'password' => bcrypt('secret'),
            'salary' => 9000,
            'phone' => 'xxxxxxxxxx',
            'ssn' => 'xxxxxxxxxx',
            'birthdate' => '1999-09-02',
            'address_id' => 1,
            'job_id' => 3,
            'created_at' => Date("Y/m/d")
        ]);
        $faker = Faker::create();
        foreach (range(1, 10) as $index) {
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
                'job_id' => $faker->numberBetween(3, 10),
                'created_at' => Date("Y/m/d")
            ]);
        }
    }
}
