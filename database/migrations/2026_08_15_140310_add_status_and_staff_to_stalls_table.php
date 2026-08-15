<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stalls', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('name');
            $table->string('description')->nullable()->after('is_active');
            $table->foreignId('staff_id')->nullable()->after('description')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('stalls', function (Blueprint $table) {
            $table->dropConstrainedForeignId('staff_id');
            $table->dropColumn(['is_active', 'description']);
        });
    }
};
