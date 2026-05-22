<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_logs', function (Blueprint $table) {
            $table->id();
            $table->string('unloading_entry_id');
            $table->foreign('unloading_entry_id')->references('id')->on('unloading_entries')->cascadeOnDelete();
            $table->string('type'); // 'truck', 'material', 'audio'
            $table->string('file_path');
            $table->string('caption')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_logs');
    }
};
