<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::firstOrCreate(
            ['email' => 'admin@fintks.com'],
            [
                'name' => 'المدير العام',
                'password' => Hash::make('12345678'),
            ]
        );
        
        // Create 10 additional non-admin users
        User::factory()->count(10)->create();
    }
}
