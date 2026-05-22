<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('unloading_entries', function (Blueprint $table) {
            $table->decimal('latitude',  10, 7)->nullable()->after('status');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->decimal('gps_accuracy', 8, 2)->nullable()->after('longitude'); // metres
        });
    }

    public function down(): void
    {
        Schema::table('unloading_entries', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'gps_accuracy']);
        });
    }
};
