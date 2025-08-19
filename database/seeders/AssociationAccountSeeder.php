<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bank;
use App\Models\CashBox;
use App\Models\Currency;
use App\Models\Project;

class AssociationAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currencies = Currency::all();
        $banks = Bank::all();
        $cashBoxes = CashBox::all();
        $projects = Project::all();

        $accounts = $banks->concat($cashBoxes);

        foreach ($accounts as $account) {
            foreach ($currencies as $currency) {
                $account->currencies()->syncWithoutDetaching([
                    $currency->id => ['balance' => 0]
                ]);
            }
        }

        foreach ($projects as $project) {
            foreach ($currencies as $currency) {
                $project->currencies()->syncWithoutDetaching([
                    $currency->id => ['balance' => 0, 'total_donations' => 0, 'total_payments' => 0, 'total_expenses' => 0]
                ]);
            }
        }
    }
}
