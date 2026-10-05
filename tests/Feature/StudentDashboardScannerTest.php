<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->student = User::create([
        'name' => 'Dash Student',
        'email' => 'dash@example.com',
        'password' => bcrypt('password123'),
        'role' => 'student',
        'email_verified_at' => now(),
    ]);
});

test('the dashboard carries the working scanner, not a static instruction', function () {
    $response = $this->actingAs($this->student)->get(route('student.dashboard'))->assertOk();

    $response->assertSee('id="qr-viewfinder"', false)
        ->assertSee('id="qr-start"', false)
        ->assertSee('id="qr-photo"', false)
        ->assertSee('Start scanning', false)
        ->assertSee('Scan a saved image', false)
        ->assertSee('jsqr@1.4.0/dist/jsQR.js', false);
});

// The scanner is how a student reaches a stall, so it does not depend on the stall list.
test('the dashboard scanner renders when no stall is open for evaluation', function () {
    DB::table('stalls')->insert([
        'name' => 'Closed Canteen', 'is_active' => false, 'created_at' => now(), 'updated_at' => now(),
    ]);

    $this->actingAs($this->student)->get(route('student.dashboard'))
        ->assertOk()
        ->assertSee('id="qr-start"', false)
        ->assertSee('No Food Stalls Open for Evaluation');
});

// A phone on plain http, with the camera blocked, or with no camera must still have a way in.
test('the dashboard scanner explains the saved-image fallback for every camera failure', function () {
    $response = $this->actingAs($this->student)->get(route('student.dashboard'))->assertOk();

    $response->assertSee('Live scanning needs a secure (https) connection. Use "Scan a saved image" instead.', false)
        ->assertSee('Camera access was blocked. Allow it in your browser settings, or use "Scan a saved image".', false)
        ->assertSee('No camera was found on this device. Use "Scan a saved image" instead.', false);
});

// Two copies on one page would duplicate the IDs the scanner's script looks up.
test('the dashboard includes the scanner exactly once', function () {
    $content = $this->actingAs($this->student)->get(route('student.dashboard'))->getContent();

    expect(substr_count($content, 'id="qr-start"'))->toBe(1);
    expect(substr_count($content, 'id="qr-viewfinder"'))->toBe(1);
});

// ADDED: the scanner is no longer inline; it sits in a dialog the floating button opens.
test('the dashboard scanner sits inside a dialog with triggers to open it', function () {
    $content = $this->actingAs($this->student)->get(route('student.dashboard'))->getContent();

    expect($content)->toContain('<dialog id="qr-scan-modal"');
    // The dialog is rendered after the page content, so the viewfinder must come after its opening tag.
    expect(strpos($content, '<dialog id="qr-scan-modal"'))->toBeLessThan(strpos($content, 'id="qr-viewfinder"'));
    // The floating button is the only trigger.
    expect(substr_count($content, 'class="js-open-scanner'))->toBe(1);
    expect($content)->toContain('js-close-scanner');
});

// The dashboard still has one page heading after the scanner card is added.
test('the dashboard has exactly one h1', function () {
    $content = $this->actingAs($this->student)->get(route('student.dashboard'))->getContent();

    expect(substr_count($content, '<h1'))->toBe(1);
});
