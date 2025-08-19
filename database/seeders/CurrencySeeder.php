<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Currency;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('currencies')->truncate();
        Schema::enableForeignKeyConstraints();

        $currencies = [
            ['name' => 'دولار أمريكي', 'code' => 'USD', 'symbol' => '$', 'exchange_rate' => 1.00, 'is_default' => true],
            ['name' => 'يورو', 'code' => 'EUR', 'symbol' => '€', 'exchange_rate' => 0.93, 'is_default' => false],
            ['name' => 'جنيه استرليني', 'code' => 'GBP', 'symbol' => '£', 'exchange_rate' => 0.81, 'is_default' => false],
            ['name' => 'شيكل', 'code' => 'ILS', 'symbol' => '₪', 'exchange_rate' => 3.75, 'is_default' => false],
            ['name' => 'دينار أردني', 'code' => 'JOD', 'symbol' => 'JD', 'exchange_rate' => 0.71, 'is_default' => false],
            ['name' => 'ليرة تركية', 'code' => 'TRY', 'symbol' => '₺', 'exchange_rate' => 32.20, 'is_default' => false],
        ];

        foreach ($currencies as $currency) {
            Currency::create($currency);
        }
    }
}
