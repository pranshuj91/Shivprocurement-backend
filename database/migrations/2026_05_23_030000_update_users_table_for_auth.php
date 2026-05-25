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
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
            $table->string('password')->nullable()->change();
            $table->string('phone')->nullable()->unique()->after('password');
            $table->string('pin')->nullable()->after('phone');
            $table->string('role')->default('manager')->after('pin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Note: SQLite change back to not-null might require values, but fresh migrations work fine
            $table->string('email')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
            $table->dropColumn(['phone', 'pin', 'role']);
        });
    }
};
