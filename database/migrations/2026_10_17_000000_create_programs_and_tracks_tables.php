<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('tracks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->restrictOnDelete();
            $table->string('code', 20);
            $table->string('name');
            $table->timestamps();
            $table->unique(['program_id', 'code']);
            $table->unique(['id', 'program_id']);
        });

        $programs = [
            'BSIT' => ['Bachelor of Science in Information Technology', [
                'BA'   => 'Business Analytics',
                'WMAD' => 'Web and Mobile Application Development',
                'NS'   => 'Network Security',
                'BPO'  => 'Business Process Outsourcing',
            ]],
            'BSCS' => ['Bachelor of Science in Computer Science', [
                'BA' => 'Business Analytics',
                'DM' => 'Data Mining',
            ]],
            'BSEMC' => ['Bachelor of Science in Entertainment and Multimedia Computing', [
                'GD' => 'Game Development',
                'DA' => 'Digital Animation',
            ]],
        ];

        foreach ($programs as $code => [$name, $tracks]) {
            $programId = DB::table('programs')->insertGetId(['code' => $code, 'name' => $name, 'created_at' => now(), 'updated_at' => now()]);
            foreach ($tracks as $trackCode => $trackName) {
                DB::table('tracks')->insert(['program_id' => $programId, 'code' => $trackCode, 'name' => $trackName, 'created_at' => now(), 'updated_at' => now()]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tracks');
        Schema::dropIfExists('programs');
    }
};
