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
            $table->foreignId('cheque_id')->nullable()->constrained()->onDelete('set null');
        });
        Schema::table('expense_vouchers', function (Blueprint $table) {
            $table->foreignId('cheque_id')->nullable()->constrained()->onDelete('set null');
        });
        Schema::table('payment_vouchers', function (Blueprint $table) {
            $table->foreignId('cheque_id')->nullable()->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropForeign(['cheque_id']);
            $table->dropColumn('cheque_id');
        });
        Schema::table('expense_vouchers', function (Blueprint $table) {
            $table->dropForeign(['cheque_id']);
            $table->dropColumn('cheque_id');
        });
        Schema::table('payment_vouchers', function (Blueprint $table) {
            $table->dropForeign(['cheque_id']);
            $table->dropColumn('cheque_id');
        });
    }
};
