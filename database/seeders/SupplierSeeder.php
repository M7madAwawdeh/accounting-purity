<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('suppliers')->truncate();
        Schema::enableForeignKeyConstraints();

        $suppliers = [
            ['name' => 'قرطاسية الوطنية', 'contact_person' => 'سامر عودة', 'phone' => '02-295-1111', 'address' => 'المنطقة الصناعية، رام الله', 'email' => 'info@alwatania.ps'],
            ['name' => 'تكنوكمب للحواسيب', 'contact_person' => 'نادية خليل', 'phone' => '09-238-2222', 'address' => 'مركز مدينة نابلس', 'email' => 'sales@technocomp.ps'],
            ['name' => 'خدمات القدس للتموين', 'contact_person' => 'يوسف حداد', 'phone' => '02-628-3333', 'address' => 'شارع صلاح الدين، القدس', 'email' => 'contact@jcatering.ps'],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }

        Supplier::factory()->count(7)->create();
    }
}
