<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // UserSeeder を実行
        $this->call([
            UserSeeder::class,
            WeightLogSeeder::class,
        ]);
    }
}
