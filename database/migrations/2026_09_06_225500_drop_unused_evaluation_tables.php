<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop evaluation_activity_id foreign key & column from stall_evaluations
        Schema::table('stall_evaluations', function (Blueprint $table) {
            if (Schema::hasColumn('stall_evaluations', 'evaluation_activity_id')) {
                $table->dropConstrainedForeignId('evaluation_activity_id');
            }
        });

        // 2. Drop unused evaluation activities tables
        Schema::dropIfExists('evaluation_activity_stall');
        Schema::dropIfExists('evaluation_activities');

        // 3. Drop unused statements and criteria tables
        Schema::dropIfExists('statements');
        Schema::dropIfExists('criteria');
    }

    public function down(): void
    {
        // Recreate criteria
        Schema::create('criteria', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('weight', 5, 2);
            $table->boolean('is_benefit')->default(true);
            $table->timestamps();
        });

        // Recreate statements
        Schema::create('statements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('criterion_id')
                  ->constrained('criteria')
                  ->cascadeOnDelete();
            $table->text('statement');
            $table->timestamps();
        });

        // Recreate evaluation_activities
        Schema::create('evaluation_activities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Recreate evaluation_activity_stall
        Schema::create('evaluation_activity_stall', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_activity_id')->constrained('evaluation_activities')->cascadeOnDelete();
            $table->foreignId('stall_id')->constrained('stalls')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['evaluation_activity_id', 'stall_id'], 'activity_stall_unique');
        });

        // Re-add evaluation_activity_id to stall_evaluations
        Schema::table('stall_evaluations', function (Blueprint $table) {
            $table->foreignId('evaluation_activity_id')->nullable()->after('stall_id')
                ->constrained('evaluation_activities')->nullOnDelete();
        });
    }
};
