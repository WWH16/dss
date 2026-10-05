<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::create([
        'name' => 'Stalls Admin',
        'email' => 'stallsadmin@example.com',
        'password' => bcrypt('AdminPassword123!'),
        'role' => 'admin',
        'email_verified_at' => now(),
    ]);

    DB::table('stalls')->insert([
        'name' => 'Snack Hub',
        'is_active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
});

// ADDED: the Add New Stall form is a <details> panel, collapsed on phones by the script at the bottom of the
// page. It ships with `open` so desktop and no-JS keep the form expanded.
test('the add stall form is a details panel that ships open', function () {
    $response = $this->actingAs($this->admin)->get(route('admin.stalls'))
        ->assertOk()
        ->assertSee('<details id="add-stall-panel"', false)
        ->assertSee('id="add-stall-form"', false)
        ->assertSee('Add New Stall');

    expect($response->getContent())->toMatch('/<details id="add-stall-panel"[^>]*\sopen\b/');
});

// A validation error must keep the panel open, or the message is hidden behind a collapsed summary.
test('a rejected stall name marks the panel so it stays open', function () {
    $this->actingAs($this->admin)
        ->from(route('admin.stalls'))
        ->post(route('admin.stall.add'), ['name' => ''])
        ->assertSessionHasErrors('name');

    $this->actingAs($this->admin)->get(route('admin.stalls'))
        ->assertOk()
        ->assertSee('data-has-error="1"', false);
});

// ADDED: from lg up each stall row is a four-column grid. The column labels and the rows must declare the same
// grid template, or the headings stop lining up with the data under them.
test('the stall rows and their column labels share one grid template', function () {
    $content = $this->actingAs($this->admin)->get(route('admin.stalls'))->getContent();

    $template = 'lg:grid-cols-[minmax(0,1fr)_7rem_12rem_13rem]';
    expect(substr_count($content, $template))->toBe(2);
    expect($content)->toContain('>Stall</span>');
    expect($content)->toContain('>Rating</span>');
    expect($content)->toContain('>Staff</span>');
});

// On a phone the order is [add form] - [directory] - [guidelines]; desktop grid placement restores the columns.
test('the guidelines card renders after the stalls directory', function () {
    $content = $this->actingAs($this->admin)->get(route('admin.stalls'))->getContent();

    expect(strpos($content, 'id="add-stall-panel"'))
        ->toBeLessThan(strpos($content, 'id="stalls-list-container"'));
    expect(strpos($content, 'id="stalls-list-container"'))
        ->toBeLessThan(strpos($content, 'Staff &amp; Stall Guidelines'));
});
