<?php

namespace Database\Seeders;

use App\Models\Crm\ResourceModifier;
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
        $this->call(LanguageSeeder::class);
//        $this->call(PermissionTableSeeder::class);
        $this->call(UserSeeder::class);
    }
}
