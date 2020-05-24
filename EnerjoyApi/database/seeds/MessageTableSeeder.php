<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class MessageTableSeeder extends Seeder
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
            foreach (range(1, 10) as $j) {
                DB::table('messages')->insert([
                    'text' => $faker->text(50),
                    'sender_id' => $faker->randomElement([1, $i]),
                    'conversation_id' => $i,
                    'created_at' => Date("Y/m/d")
                ]);
            }
        }
    }
}
