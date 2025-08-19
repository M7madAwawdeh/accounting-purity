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
        Schema::table('cheques', function (Blueprint $table) {
            $table->renameColumn('cheque_number', 'number');
            $table->morphs('chequeable');
            $table->dropColumn(['recipient', 'payer']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cheques', function (Blueprint $table) {
            $table->renameColumn('number', 'cheque_number');
            $table->dropMorphs('chequeable');
            $table->string('recipient')->comment('Beneficiary for outgoing cheques');
            $table->string('payer')->comment('Payer for incoming cheques');
        });
    }
};
