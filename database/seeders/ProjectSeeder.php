<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\Funder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('projects')->truncate();
        Schema::enableForeignKeyConstraints();
        
        $funderIds = Funder::where('is_default', false)->pluck('id');
        $defaultFunderId = Funder::where('is_default', true)->first()->id;

        $projects = [
            [
                'name' => 'حساب الجمعية', 
                'funder_id' => $defaultFunderId, 
                'start_date' => null, 
                'end_date' => null, 
                'value' => 0, 
                'currency' => 'ILS', 
                'is_default' => true,
                'description' => 'حساب الجمعية'
            ],
            [
                'name' => 'برنامج تمكين الشباب', 
                'funder_id' => $funderIds->random(), 
                'start_date' => '2023-01-01', 
                'end_date' => '2025-12-31', 
                'value' => 0, 
                'currency' => 'USD',
                'description' => 'برنامج لتوفير التدريب المهني وتنمية المهارات القيادية للشباب.'
            ],
            [
                'name' => 'مبادرة غزة الصحية', 
                'funder_id' => $funderIds->random(), 
                'start_date' => '2023-09-15', 
                'end_date' => '2026-09-14', 
                'value' => 0, 
                'currency' => 'GBP',
                'description' => 'دعم عيادات الرعاية الصحية الأولية والوحدات الطبية المتنقلة في قطاع غزة.'
            ],
            [
                'name' => 'تنمية المشاريع الصغيرة', 
                'funder_id' => $funderIds->random(), 
                'start_date' => '2024-03-01', 
                'end_date' => '2027-02-28', 
                'value' => 0, 
                'currency' => 'USD',
                'description' => 'تقديم المنح والإرشاد لمساعدة المشاريع الصغيرة على النمو.'
            ],
        ];

        foreach ($projects as $project) {
            Project::create($project);
        }

        Project::factory()->count(5)->create();
    }
}
