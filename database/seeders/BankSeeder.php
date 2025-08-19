<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bank;
use App\Models\Currency;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('banks')->truncate();
        Schema::enableForeignKeyConstraints();

        $banksData = [
            ['name' => 'بنك فلسطين - دولار', 'account_number' => 'PS92PALS000000000400123456701', 'iban' => 'PS92PALS000000000400123456701', 'swift_code' => 'PALSPS22', 'contact_person' => 'أحمد محمود', 'phone' => '0599123456', 'address' => 'رام الله، فلسطين'],
            ['name' => 'البنك العربي - دينار', 'account_number' => 'PS27ARAB000000000090101234567', 'iban' => 'PS27ARAB000000000090101234567', 'swift_code' => 'ARABPS22', 'contact_person' => 'فاطمة علي', 'phone' => '0568765432', 'address' => 'نابلس، فلسطين'],
            ['name' => 'البنك الوطني - شيكل', 'account_number' => 'PS45TNBC000000000001002345678', 'iban' => 'PS45TNBC000000000001002345678', 'swift_code' => 'TNBCPS22', 'contact_person' => 'عمر حسن', 'phone' => '0595112233', 'address' => 'الخليل، فلسطين'],
        ];

        foreach ($banksData as $bankData) {
            Bank::create($bankData);
        }

        Bank::factory()->count(7)->create();
    }
}
