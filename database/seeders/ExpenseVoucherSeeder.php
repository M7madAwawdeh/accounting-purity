<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExpenseVoucher;
use App\Models\Project;
use App\Models\Bank;
use App\Models\CashBox;
use App\Models\Currency;
use App\Models\Supplier;
use App\Models\Employee;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ExpenseVoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('expense_vouchers')->truncate();
        Schema::enableForeignKeyConstraints();
        
        $projects = Project::all();
        $currencies = Currency::all();
        $accounts = collect(Bank::all())->concat(CashBox::all());
        $recipients = collect(Supplier::all())->concat(Employee::all());
        if ($projects->isEmpty() || $currencies->isEmpty() || $accounts->isEmpty()) {
            if (isset($this->command)) {
                $this->command->warn('Skipping DonationSeeder: Make sure projects, funders, currencies, banks, and cash boxes are seeded first.');
            }
            return;
        }
        for ($i = 0; $i < 20; $i++) {
            $project = $projects->random();
            $currency = $currencies->random();
            $account = $accounts->random();
            $recipient = $recipients->random();

            $expense = ExpenseVoucher::create([
                'project_id' => $project->id,
                'recipient' => $recipient->name,
                'amount' => rand(50, 2000),
                'currency' => $currency->code,
                'payment_method' => ['cash', 'bank_transfer', 'cheque'][rand(0, 2)],
                'date' => now()->subDays(rand(1, 365)),
                'description' => 'Seeded expense for ' . $project->name,
                'accountable_type' => get_class($account),
                'accountable_id' => $account->id,
            ]);

            // Update balances
            $account->currencies()->syncWithoutDetaching([
                $currency->id => ['balance' => $account->currencies()->where('currency_id', $currency->id)->first()->pivot->balance - $expense->amount]
            ]);

            $project->currencies()->syncWithoutDetaching([
                $currency->id => [
                    'balance' => $project->currencies()->where('currency_id', $currency->id)->first()->pivot->balance - $expense->amount,
                    'total_expenses' => $project->currencies()->where('currency_id', $currency->id)->first()->pivot->total_expenses + $expense->amount,
                ]
            ]);
        }
    }
}
