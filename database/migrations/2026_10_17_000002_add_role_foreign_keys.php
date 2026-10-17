<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stall_evaluations', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->foreign('student_id')->references('user_id')->on('students')->cascadeOnDelete();
        });

        DB::table('sessions')->whereNotNull('user_id')->whereNotIn('user_id', DB::table('users')->select('id'))->delete();
        Schema::table('sessions', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });

        DB::table('password_reset_tokens')->whereNotIn('email', DB::table('users')->select('email'))->delete();
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->foreign('email')->references('email')->on('users')->cascadeOnUpdate()->cascadeOnDelete();
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('student', 'staff', 'admin', 'clinic'))");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE users DROP CONSTRAINT users_role_check');
        }

        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->dropForeign(['email']);
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('stall_evaluations', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->foreign('student_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
