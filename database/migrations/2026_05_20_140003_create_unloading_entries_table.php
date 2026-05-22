<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unloading_entries', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();
            $table->string('truck_no');
            $table->string('purchase_type')->nullable(); // Depo / Direct / Other
            $table->string('sourced_from')->nullable();  // Location name / Depo location
            $table->decimal('moisture', 5, 2);
            $table->decimal('fm', 5, 2);
            $table->decimal('dm', 5, 2);
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unloading_entries');
    }
};
