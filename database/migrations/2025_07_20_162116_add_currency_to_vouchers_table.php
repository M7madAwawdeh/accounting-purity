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
        Schema::table('donations', function (Blueprint $table) {
            $table->string('currency')->after('amount');
        });
        Schema::table('payment_vouchers', function (Blueprint $table) {
            $table->string('currency')->after('amount');
        });
        Schema::table('expense_vouchers', function (Blueprint $table) {
            $table->string('currency')->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn('currency');
        });
        Schema::table('payment_vouchers', function (Blueprint $table) {
            $table->dropColumn('currency');
        });
        Schema::table('expense_vouchers', function (Blueprint $table) {
            $table->dropColumn('currency');
        });
    }
};
