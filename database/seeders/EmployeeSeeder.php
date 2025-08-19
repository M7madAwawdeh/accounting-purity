<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('employees')->truncate();
        Schema::enableForeignKeyConstraints();

        $employees = [
            ['name' => 'محمد جابر', 'position' => 'مدير مشاريع', 'salary' => 6000.00, 'joining_date' => '2021-03-15', 'phone' => '0599001122', 'address' => 'رام الله'],
            ['name' => 'سارة حمدان', 'position' => 'مسؤولة مالية', 'salary' => 5500.00, 'joining_date' => '2022-07-01', 'phone' => '0598112233', 'address' => 'نابلس'],
            ['name' => 'أحمد فياض', 'position' => 'منسق ميداني', 'salary' => 4800.00, 'joining_date' => '2023-01-20', 'phone' => '0569223344', 'address' => 'الخليل'],
        ];

        foreach ($employees as $employee) {
            Employee::create($employee);
        }

        Employee::factory()->count(12)->create();
    }
}
