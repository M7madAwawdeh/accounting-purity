<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Core Data
            CurrencySeeder::class,
            AdminUserSeeder::class,

            // People & Accounts
            FunderSeeder::class,
            MemberSeeder::class,
            SupplierSeeder::class,
            EmployeeSeeder::class,
            BankSeeder::class,
            CashBoxSeeder::class, 
            
            // Projects (depends on Funders)
            ProjectSeeder::class, 
            
            // Link accounts/projects with all currencies (MUST RUN AFTER ACCOUNTS/PROJECTS)
            AssociationAccountSeeder::class,

            // Financial transactions (Must be last)
            DonationSeeder::class,
            ExpenseVoucherSeeder::class,
            PaymentVoucherSeeder::class,
        ]);
    }
}
