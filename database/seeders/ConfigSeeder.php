<?php

namespace Database\Seeders;

use App\Models\Crm\Config;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $parent = Config::updateOrCreate([
            'id' => 'grid-pagination-limit',
            'value' => 15
        ]);

        $parent = Config::updateOrCreate([
            'id' => 'choice-subject-deadline',
            'value' => 10
        ]);

    }
}
