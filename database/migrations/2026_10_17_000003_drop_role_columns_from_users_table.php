<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('stall_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['course', 'track', 'year_level', 'student_number']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('course')->nullable();
            $table->string('track')->nullable()->after('course');
            $table->string('year_level')->nullable();
            $table->string('student_number')->nullable();
            $table->foreignId('stall_id')->nullable()->constrained('stalls')->nullOnDelete();
        });

        DB::table('users')->update([
            'course' => DB::raw('(SELECT programs.code FROM students JOIN programs ON programs.id = students.program_id WHERE students.user_id = users.id)'),
            'track' => DB::raw('(SELECT tracks.code FROM students JOIN tracks ON tracks.id = students.track_id WHERE students.user_id = users.id)'),
            'year_level' => DB::raw('(SELECT students.year_level FROM students WHERE students.user_id = users.id)'),
            'student_number' => DB::raw('(SELECT students.student_number FROM students WHERE students.user_id = users.id)'),
            'stall_id' => DB::raw('(SELECT staff.stall_id FROM staff WHERE staff.user_id = users.id)'),
        ]);
    }
};
