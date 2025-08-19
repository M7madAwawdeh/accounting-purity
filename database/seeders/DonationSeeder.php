<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Donation;
use App\Models\Project;
use App\Models\Bank;
use App\Models\CashBox;
use App\Models\Currency;
use App\Models\Funder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DonationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('donations')->truncate();
        Schema::enableForeignKeyConstraints();
        
        $projects = Project::all();
        $funders = Funder::all();
        $currencies = Currency::all();
        $accounts = collect(Bank::all())->concat(CashBox::all());

        if ($projects->isEmpty() || $funders->isEmpty() || $currencies->isEmpty() || $accounts->isEmpty()) {
            if (isset($this->command)) {
                $this->command->warn('Skipping DonationSeeder: Make sure projects, funders, currencies, banks, and cash boxes are seeded first.');
            }
            return;
        }

        for ($i = 0; $i < 15; $i++) {
            $project = $projects->random();
            $funder = $funders->random();
            $currency = $currencies->random();
            $account = $accounts->random();

            $donation = Donation::create([
                'project_id' => $project->id,
                'funder_id' => $funder->id,
                'donor_name' => $funder->name, 
                'amount' => rand(500, 10000),
                'currency' => $currency->code,
                'payment_method' => ['cash', 'bank_transfer', 'cheque'][rand(0, 2)],
                'date' => now()->subDays(rand(1, 365)),
                'description' => 'تبرع لمشروع ' . $project->name,
                'accountable_type' => get_class($account),
                'accountable_id' => $account->id,
            ]);

            // Update balances
            $account->currencies()->syncWithoutDetaching([
                $currency->id => ['balance' => $account->currencies()->where('currency_id', $currency->id)->first()->pivot->balance + $donation->amount]
            ]);

            $project->currencies()->syncWithoutDetaching([
                $currency->id => [
                    'balance' => $project->currencies()->where('currency_id', $currency->id)->first()->pivot->balance + $donation->amount,
                    'total_donations' => $project->currencies()->where('currency_id', $currency->id)->first()->pivot->total_donations + $donation->amount,
                    ]
            ]);
        }
    }
}
