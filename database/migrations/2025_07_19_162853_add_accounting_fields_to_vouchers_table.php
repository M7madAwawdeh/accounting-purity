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
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('set null');
            $table->morphs('accountable'); // This will create accountable_id and accountable_type
        });

        Schema::table('expense_vouchers', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('set null');
            $table->morphs('accountable');
        });

        Schema::table('payment_vouchers', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('set null');
            $table->morphs('accountable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropMorphs('accountable');
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });

        Schema::table('expense_vouchers', function (Blueprint $table) {
            $table->dropMorphs('accountable');
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });

        Schema::table('payment_vouchers', function (Blueprint $table) {
            $table->dropMorphs('accountable');
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });
    }
};
