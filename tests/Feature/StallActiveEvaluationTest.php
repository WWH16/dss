<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->student = User::create([
        'name' => 'Test Student',
        'email' => 'student@example.com',
        'password' => bcrypt('password123'),
        'role' => 'student',
        'email_verified_at' => now(),
    ]);

    $this->activeStallId = DB::table('stalls')->insertGetId([
        'name' => 'Active Canteen',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->inactiveStallId = DB::table('stalls')->insertGetId([
        'name' => 'Closed Canteen',
        'is_active' => false,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
});

// CHANGED: evaluations open only from a stall's signed QR link, so these tests "scan" first.
function scanUrl(int $stallId): string
{
    return URL::signedRoute('student.evaluation', ['stall' => $stallId], null, false);
}

function allRatings(int $value): array
{
    return array_fill_keys(range(1, 10), $value);
}

test('without a scan the evaluation page asks the student to scan a QR code', function () {
    $this->actingAs($this->student)->get(route('student.evaluation'))
        ->assertOk()
        ->assertSee("Scan the stall's QR code", false)
        ->assertSee('id="qr-start"', false)
        ->assertSee('id="qr-photo"', false)
        ->assertDontSee('id="evaluationForm"', false);
});

test('scanning an active stall opens its form with the stall named', function () {
    $this->actingAs($this->student)->get(scanUrl($this->activeStallId))
        ->assertOk()
        ->assertSee('You are evaluating')
        ->assertSee('Active Canteen')
        ->assertDontSee('Closed Canteen');
});

test('an unsigned or tampered stall link does not open a form', function () {
    $this->actingAs($this->student)->get(route('student.evaluation', ['stall' => $this->activeStallId]))
        ->assertOk()
        ->assertSee("Scan the stall's QR code", false);

    $tampered = str_replace('stall=' . $this->activeStallId, 'stall=' . $this->inactiveStallId, scanUrl($this->activeStallId));
    $this->actingAs($this->student)->get($tampered)
        ->assertRedirect(route('student.evaluation'))
        ->assertSessionHas('error', 'This QR code is not valid. Scan the code posted at the stall.');
});

test('scanning an inactive stall redirects with an error flash message', function () {
    $this->actingAs($this->student)->get(scanUrl($this->inactiveStallId))
        ->assertRedirect(route('student.evaluation'))
        ->assertSessionHas('error', 'Closed Canteen is currently closed for student evaluations.');
});

test('submitting without scanning is rejected and not saved', function () {
    $this->actingAs($this->student)->post(route('student.evaluation.store'), [
        'stall_id' => $this->activeStallId,
        'responses' => allRatings(4),
    ])->assertSessionHas('error', 'Scan the QR code at the stall to evaluate it.');

    $this->assertDatabaseCount('stall_evaluations', 0);
});

test('a stall closed after scanning is rejected and not saved', function () {
    $this->actingAs($this->student)->get(scanUrl($this->activeStallId));
    DB::table('stalls')->where('id', $this->activeStallId)->update(['is_active' => false]);

    $this->actingAs($this->student)->post(route('student.evaluation.store'), [
        'responses' => allRatings(5),
    ])->assertSessionHasErrors(['stall_id']);

    $this->assertDatabaseCount('stall_evaluations', 0);
});

test('submitting after a scan saves to the scanned stall, ignoring any stall_id in the form', function () {
    $otherStallId = DB::table('stalls')->insertGetId([
        'name' => 'Other Canteen', 'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
    ]);

    $this->actingAs($this->student)->get(scanUrl($this->activeStallId));
    $this->actingAs($this->student)->post(route('student.evaluation.store'), [
        'stall_id' => $otherStallId,
        'responses' => allRatings(4),
        'comment' => 'Great food!',
    ])->assertSessionHas('success', 'Evaluation submitted successfully!');

    $this->assertDatabaseHas('stall_evaluations', [
        'stall_id' => $this->activeStallId,
        'student_id' => $this->student->id,
        'comment' => 'Great food!',
    ]);
    $this->assertDatabaseMissing('stall_evaluations', ['stall_id' => $otherStallId]);
});

test('inactive stalls do not appear on the student dashboard', function () {
    $response = $this->actingAs($this->student)->get(route('student.dashboard'));

    $response->assertStatus(200);
    $response->assertSee('Active Canteen');
    $response->assertDontSee('Closed Canteen');
});

test('the stall list on the student dashboard starts collapsed', function () {
    $this->actingAs($this->student)->get(route('student.dashboard'))
        ->assertSee('<details id="stallsDirectory" class=', false)
        ->assertDontSee('<details id="stallsDirectory" open', false)
        // CHANGED: the dashboard's greys moved from Tailwind's neutral-* defaults to the project's ink-* tokens.
        ->assertSee('of <span class="font-bold text-ink-700 tabular-nums">1</span> open stall', false);
});

test('the student dashboard has no direct evaluation links to stalls', function () {
    $this->actingAs($this->student)->get(route('student.dashboard'))
        ->assertDontSee('stall=' . $this->activeStallId, false);
});

test('submitted ratings are averaged per criterion', function () {
    $responses = [1 => 5, 2 => 4, 3 => 3, 4 => 2, 5 => 3, 6 => 1, 7 => 2, 8 => 2, 9 => 5, 10 => 4];

    $this->actingAs($this->student)->get(scanUrl($this->activeStallId));
    $this->actingAs($this->student)->post(route('student.evaluation.store'), [
        'responses' => $responses,
    ])->assertSessionHas('success');

    $row = DB::table('stall_evaluations')->first();
    expect((float) $row->taste)->toBe(4.0);
    expect((float) $row->price)->toBe(2.5);
    expect((float) $row->cleanliness)->toBe(1.67);
    expect((float) $row->service)->toBe(4.5);
});

// ADDED: the scanner markup, CSS and script now come from partials/qr-scanner.blade.php; these
// assertions pin the pieces that the extraction could silently drop.
test('the evaluation page renders the shared scanner partial in full', function () {
    $response = $this->actingAs($this->student)->get(route('student.evaluation'))->assertOk();

    $response->assertSee('id="qr-viewfinder"', false)
        ->assertSee('id="qr-video"', false)
        ->assertSee('id="qr-status"', false)
        ->assertSee('id="qr-start"', false)
        ->assertSee('id="qr-photo"', false)
        ->assertSee('Start scanning', false)
        ->assertSee('Scan a saved image', false)
        ->assertSee('jsqr@1.4.0/dist/jsQR.js', false)
        ->assertSee('@media (prefers-reduced-motion: reduce)', false)
        ->assertSee("That QR code isn't a stall evaluation code.", false)
        ->assertSee('was made for', false);

    expect(substr_count($response->getContent(), 'id="qr-start"'))->toBe(1);
});

// ADDED: a student may submit one evaluation per calendar month (Philippine time), whichever stall it is for.
function pastEvaluation(int $studentId, int $stallId, string $createdAtUtc): void
{
    DB::table('stall_evaluations')->insert([
        'student_id' => $studentId,
        'stall_id' => $stallId,
        'cleanliness' => 4, 'service' => 4, 'taste' => 4, 'price' => 4,
        'created_at' => $createdAtUtc,
        'updated_at' => $createdAtUtc,
    ]);
}

test('a second evaluation in the same month is rejected even for a different stall', function () {
    $this->travelTo(Carbon::parse('2026-10-20 03:00:00', 'UTC'));
    $otherStallId = DB::table('stalls')->insertGetId([
        'name' => 'Other Canteen', 'is_active' => true, 'created_at' => now(), 'updated_at' => now(),
    ]);
    pastEvaluation($this->student->id, $otherStallId, '2026-10-02 01:00:00');

    $this->actingAs($this->student)->withSession(['qr_stall_id' => $this->activeStallId])
        ->followingRedirects()
        ->post(route('student.evaluation.store'), ['responses' => allRatings(4)])
        ->assertSee('Already evaluated this month')
        ->assertSee('You have already submitted your evaluation for October. You can evaluate a stall again on November 1.');

    $this->assertDatabaseCount('stall_evaluations', 1);
});

test('the month follows Philippine time, not UTC', function () {
    // Nov 1, 00:30 in Manila is still Oct 31 in UTC.
    $this->travelTo(Carbon::parse('2026-10-31 16:30:00', 'UTC'));
    pastEvaluation($this->student->id, $this->activeStallId, '2026-10-10 04:00:00');

    $this->actingAs($this->student)->withSession(['qr_stall_id' => $this->activeStallId])
        ->post(route('student.evaluation.store'), ['responses' => allRatings(4)])
        ->assertSessionHas('success', 'Evaluation submitted successfully!');

    // The evaluation just saved counts for November, so a later November submission is blocked.
    $this->travelTo(Carbon::parse('2026-11-15 04:00:00', 'UTC'));
    $this->actingAs($this->student)->withSession(['qr_stall_id' => $this->activeStallId])
        ->followingRedirects()
        ->post(route('student.evaluation.store'), ['responses' => allRatings(4)])
        ->assertSee('You have already submitted your evaluation for November. You can evaluate a stall again on December 1.');

    $this->assertDatabaseCount('stall_evaluations', 2);
});

test('a December evaluation opens again on January 1', function () {
    $this->travelTo(Carbon::parse('2026-12-10 03:00:00', 'UTC'));
    pastEvaluation($this->student->id, $this->activeStallId, '2026-12-01 03:00:00');

    $this->actingAs($this->student)->withSession(['qr_stall_id' => $this->activeStallId])
        ->followingRedirects()
        ->post(route('student.evaluation.store'), ['responses' => allRatings(4)])
        ->assertSee('You have already submitted your evaluation for December. You can evaluate a stall again on January 1.');
});

test("another student's evaluation does not use up this student's month", function () {
    $this->travelTo(Carbon::parse('2026-10-20 03:00:00', 'UTC'));
    $classmate = User::create([
        'name' => 'Classmate', 'email' => 'classmate@example.com', 'password' => bcrypt('password123'),
        'role' => 'student', 'email_verified_at' => now(),
    ]);
    pastEvaluation($classmate->id, $this->activeStallId, '2026-10-05 02:00:00');

    $this->actingAs($this->student)->withSession(['qr_stall_id' => $this->activeStallId])
        ->post(route('student.evaluation.store'), ['responses' => allRatings(4)])
        ->assertSessionHas('success', 'Evaluation submitted successfully!');

    $this->assertDatabaseCount('stall_evaluations', 2);
});

test('the success screen says when the next evaluation opens and offers no second stall', function () {
    $this->travelTo(Carbon::parse('2026-10-20 03:00:00', 'UTC'));

    $this->actingAs($this->student)->withSession(['qr_stall_id' => $this->activeStallId])
        ->followingRedirects()
        ->post(route('student.evaluation.store'), ['responses' => allRatings(4)])
        ->assertOk()
        ->assertSee('Evaluation Submitted!')
        ->assertSee('You can evaluate a stall again on November 1.')
        ->assertDontSee('Evaluate Another Stall');
});

test("scanning a stall after this month's evaluation shows the limit instead of the form", function () {
    $this->travelTo(Carbon::parse('2026-10-20 03:00:00', 'UTC'));
    pastEvaluation($this->student->id, $this->activeStallId, '2026-10-05 02:00:00');

    $this->actingAs($this->student)->get(scanUrl($this->activeStallId))
        ->assertOk()
        ->assertSee('Already evaluated this month')
        ->assertSee('You have already submitted your evaluation for October. You can evaluate a stall again on November 1.')
        ->assertDontSee('id="evaluationForm"', false)
        ->assertDontSee('id="qr-start"', false)
        ->assertDontSee('arrow-back-outline', false)
        ->assertSessionMissing('qr_stall_id');
});

test('a scan-and-submit lands on the success screen, not the limit error', function () {
    $this->travelTo(Carbon::parse('2026-10-20 03:00:00', 'UTC'));

    $this->actingAs($this->student)->get(scanUrl($this->activeStallId))->assertOk();

    $this->actingAs($this->student)->from(scanUrl($this->activeStallId))
        ->followingRedirects()
        ->post(route('student.evaluation.store'), ['responses' => allRatings(4)])
        ->assertOk()
        ->assertSee('Evaluation Submitted!')
        ->assertDontSee('You have already submitted');
});
