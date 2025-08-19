<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CashBox;
use App\Models\Currency;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CashBoxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('cash_boxes')->truncate();
        Schema::enableForeignKeyConstraints();

        $cashBoxesData = [
            ['name' => 'الصندوق الرئيسي - رام الله', 'location' => 'المكتب الرئيسي، الطابق الثالث', 'manager' => 'علياء حسين'],
            ['name' => 'صندوق النثريات - نابلس', 'location' => 'فرع نابلس', 'manager' => 'سامي إبراهيم'],
            ['name' => 'صندوق الفعاليات - الخليل', 'location' => 'مركز مجتمع الخليل', 'manager' => 'ليلى خليل'],
        ];

        foreach ($cashBoxesData as $cashBoxData) {
            CashBox::create($cashBoxData);
        }
        
        CashBox::factory()->count(7)->create();
    }
}
