<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stalls', function (Blueprint $table) {
            if (Schema::hasColumn('stalls', 'staff_id')) {
                $table->dropConstrainedForeignId('staff_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('stalls', function (Blueprint $table) {
            $table->foreignId('staff_id')->nullable()->after('description')
                ->constrained('users')->nullOnDelete();
        });
    }
};
