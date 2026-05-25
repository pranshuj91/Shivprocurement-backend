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
        Schema::table('unloading_entries', function (Blueprint $table) {
            $table->decimal('gross_weight', 8, 3)->nullable();
            $table->decimal('tare_weight', 8, 3)->nullable();
            $table->decimal('net_weight', 8, 3)->nullable();
            $table->text('remarks')->nullable();
            $table->string('operator_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unloading_entries', function (Blueprint $table) {
            $table->dropColumn(['gross_weight', 'tare_weight', 'net_weight', 'remarks', 'operator_name']);
        });
    }
};
