<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->decimal('total_donations', 15, 2)->default(0)->after('value');
            $table->decimal('total_payments', 15, 2)->default(0)->after('total_donations');
            $table->decimal('total_expenses', 15, 2)->default(0)->after('total_payments');
            $table->decimal('association_fee_percentage', 5, 2)->default(0)->after('total_expenses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['total_donations', 'total_payments', 'total_expenses', 'association_fee_percentage']);
        });
    }
};
