<?php

namespace Database\Seeders;

use App\Models\Setting\FinanceSetting;
use Illuminate\Database\Seeder;

class FinanceSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FinanceSetting::create([
            'overtime_rate_hour' => 50,
        ]);
    }
}
