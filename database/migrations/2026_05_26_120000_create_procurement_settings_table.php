<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('procurement_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('moisture_threshold', 5, 2)->default(10.0);
            $table->decimal('fm_threshold', 5, 2)->default(2.0);
            $table->decimal('dm_threshold', 5, 2)->default(2.0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procurement_settings');
    }
};
