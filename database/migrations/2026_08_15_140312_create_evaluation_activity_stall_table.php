<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluation_activity_stall', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_activity_id')->constrained('evaluation_activities')->cascadeOnDelete();
            $table->foreignId('stall_id')->constrained('stalls')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['evaluation_activity_id', 'stall_id'], 'activity_stall_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluation_activity_stall');
    }
};
