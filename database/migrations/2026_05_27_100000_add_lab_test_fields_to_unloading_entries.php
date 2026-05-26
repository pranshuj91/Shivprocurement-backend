<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('unloading_entries', function (Blueprint $table) {
            $table->string('lab_name')->nullable()->after('remarks');
            $table->string('lab_test_status')->nullable()->after('lab_name');
            $table->decimal('lab_moisture', 5, 2)->nullable()->after('lab_test_status');
            $table->decimal('lab_fm', 5, 2)->nullable()->after('lab_moisture');
            $table->decimal('lab_dm', 5, 2)->nullable()->after('lab_fm');
            $table->timestamp('lab_recorded_at')->nullable()->after('lab_dm');
            $table->foreignId('lab_recorded_by')->nullable()->after('lab_recorded_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('unloading_entries', function (Blueprint $table) {
            $table->dropForeign(['lab_recorded_by']);
            $table->dropColumn([
                'lab_name',
                'lab_test_status',
                'lab_moisture',
                'lab_fm',
                'lab_dm',
                'lab_recorded_at',
                'lab_recorded_by',
            ]);
        });
    }
};
