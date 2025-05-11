<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 其他Seeder调用...
        $this->call(UserSeeder::class);
    }
}
