<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Funder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FunderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('funders')->truncate();
        Schema::enableForeignKeyConstraints();

        $funders = [
            ['name' => 'الصندوق العام', 'address' => 'N/A', 'is_default' => true, 'contact_person' => 'N/A', 'phone' => 'N/A', 'email' => 'N/A'],
            ['name' => 'قطر الخيرية', 'address' => 'الدوحة، قطر', 'contact_person' => 'السيد علي الكعبي', 'phone' => '+974 4466 7777', 'email' => 'info@qcharity.org'],
            ['name' => 'مؤسسة التعاون', 'address' => 'رام الله، فلسطين', 'contact_person' => 'د. لينا جابر', 'phone' => '+970 2 295 9966', 'email' => 'info@welfare-association.org'],
            ['name' => 'الاتحاد الأوروبي', 'address' => 'بروكسل، بلجيكا', 'contact_person' => 'مكتب وفد الاتحاد الأوروبي', 'phone' => '+972 2 541 5888', 'email' => 'delegation-west-bank-gaza@eeas.europa.eu'],
            ['name' => 'برنامج الأمم المتحدة الإنمائي', 'address' => 'نيويورك، الولايات المتحدة الأمريكية', 'contact_person' => 'مكتب PAPP', 'phone' => '+972 2 626 8200', 'email' => 'papp@undp.org'],
        ];

        foreach ($funders as $funder) {
            Funder::create($funder);
        }
        
        Funder::factory()->count(5)->create();
    }
}
