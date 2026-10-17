<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained()->cascadeOnDelete();
            $table->string('student_number')->nullable()->unique();
            $table->foreignId('program_id')->nullable()->constrained()->restrictOnDelete();
            $table->unsignedBigInteger('track_id')->nullable();
            $table->string('year_level')->nullable();
            $table->timestamps();
            $table->foreign(['track_id', 'program_id'])->references(['id', 'program_id'])->on('tracks')->restrictOnDelete();
        });

        Schema::create('staff', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained()->cascadeOnDelete();
            $table->foreignId('stall_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        foreach (['admins', 'clinics'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->foreignId('user_id')->primary()->constrained()->cascadeOnDelete();
                $table->timestamps();
            });
        }

        DB::table('students')->insertUsing(
            ['user_id', 'student_number', 'program_id', 'track_id', 'year_level', 'created_at', 'updated_at'],
            DB::table('users')
                ->leftJoin('programs', 'programs.code', '=', DB::raw('UPPER(TRIM(users.course))'))
                ->leftJoin('tracks', function ($join) {
                    $join->on('tracks.program_id', '=', 'programs.id')
                        ->on('tracks.code', '=', DB::raw('UPPER(TRIM(users.track))'));
                })
                ->where('users.role', 'student')
                ->select('users.id', DB::raw("NULLIF(TRIM(users.student_number), '')"), 'programs.id', 'tracks.id', 'users.year_level', 'users.created_at', 'users.updated_at')
        );

        DB::table('staff')->insertUsing(
            ['user_id', 'stall_id', 'created_at', 'updated_at'],
            DB::table('users')->where('role', 'staff')->select('id', 'stall_id', 'created_at', 'updated_at')
        );

        foreach (['admin' => 'admins', 'clinic' => 'clinics'] as $role => $table) {
            DB::table($table)->insertUsing(
                ['user_id', 'created_at', 'updated_at'],
                DB::table('users')->where('role', $role)->select('id', 'created_at', 'updated_at')
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('clinics');
        Schema::dropIfExists('admins');
        Schema::dropIfExists('staff');
        Schema::dropIfExists('students');
    }
};
