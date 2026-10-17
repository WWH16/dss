<?php

use App\Models\Program;
use App\Models\Track;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

test('programs and tracks are seeded from the old PROGRAMS list', function () {
    expect(Program::orderBy('id')->pluck('name', 'code')->all())->toBe([
        'BSIT'  => 'Bachelor of Science in Information Technology',
        'BSCS'  => 'Bachelor of Science in Computer Science',
        'BSEMC' => 'Bachelor of Science in Entertainment and Multimedia Computing',
    ]);
    expect(Program::firstWhere('code', 'BSIT')->tracks->pluck('code')->all())->toBe(['BA', 'WMAD', 'NS', 'BPO']);
    expect(Track::where('code', 'BA')->count())->toBe(2);
});

test('a track code is unique within its program', function () {
    $bsit = Program::where('code', 'BSIT')->value('id');
    expect(fn () => DB::table('tracks')->insert(['program_id' => $bsit, 'code' => 'WMAD', 'name' => 'Duplicate']))
        ->toThrow(QueryException::class);
});
