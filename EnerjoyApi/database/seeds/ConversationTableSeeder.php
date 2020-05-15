<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ConversationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        foreach (range(1, 5) as $i) {
            DB::table('conversations')->insert([
                'employee_one_id' => 1,
                'employee_two_id' => $i,
                'updated_at' => Date("Y/m/d"),
                'created_at' => Date("Y/m/d")
            ]);
        }
    }
}
