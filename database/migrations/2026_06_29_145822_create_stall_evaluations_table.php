<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stall_evaluations', function (Blueprint $table) {

            $table->id();

            $table->foreignId('student_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->foreignId('stall_id')
                  ->constrained('stalls')
                  ->cascadeOnDelete();

            $table->decimal('cleanliness',3,2);
            $table->decimal('service',3,2);
            $table->decimal('taste',3,2);
            $table->decimal('price',3,2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stall_evaluations');
    }
};
