<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stall_evaluations', function (Blueprint $table) {
            $table->foreignId('evaluation_activity_id')->nullable()->after('stall_id')
                ->constrained('evaluation_activities')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('stall_evaluations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('evaluation_activity_id');
        });
    }
};
