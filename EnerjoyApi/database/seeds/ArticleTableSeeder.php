<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class ArticleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        for ($i = 1; $i <= 20; $i++) {
            DB::table('articles')->insert([
                'title' => $faker->sentence(),
                'description' => $faker->text($maxNbChars = 200),
                'creator_id' => $faker->numberBetween(1, 10),
                'created_at' => Date("Y/m/d")
            ]);
        }
    }
}
