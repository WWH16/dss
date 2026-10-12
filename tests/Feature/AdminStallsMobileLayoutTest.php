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

    // CHANGED: template narrowed with the icon-only actions column (was minmax(0,1fr)_7rem_12rem_13rem).
    $template = 'lg:grid-cols-[minmax(0,1fr)_6rem_11rem_5rem]';
    expect(substr_count($content, $template))->toBe(2);
    expect($content)->toContain('>Stall</span>');
    expect($content)->toContain('>Rating</span>');
    expect($content)->toContain('>Staff</span>');
});

// CHANGED: the guidelines moved inside the Add New Stall panel, under the form, so they collapse with it on
// phones and sit beside the directory on desktop. Was: guidelines rendered after the directory.
test('the guidelines render inside the add stall panel, before the directory', function () {
    $content = $this->actingAs($this->admin)->get(route('admin.stalls'))->getContent();

    $panelStart = strpos($content, 'id="add-stall-panel"');
    $panelEnd = strpos($content, '</details>', $panelStart);
    $guidelines = strpos($content, 'Staff &amp; Stall Guidelines');

    expect($guidelines)->toBeGreaterThan($panelStart);
    expect($guidelines)->toBeLessThan($panelEnd);
    expect($panelEnd)->toBeLessThan(strpos($content, 'id="stalls-list-container"'));
});

// ADDED: Delete left the directory rows; the only delete control is in the Edit dialog, behind the confirmation.
test('stall rows carry no delete button and the edit dialog does', function () {
    $content = $this->actingAs($this->admin)->get(route('admin.stalls'))->getContent();

    $list = substr($content, strpos($content, 'id="stalls-list-container"'), strpos($content, 'id="edit-modal"') - strpos($content, 'id="stalls-list-container"'));
    expect($list)->not->toContain('openDeleteModal');
    expect($content)->toContain('id="edit-modal-delete-btn"');
    expect($content)->toContain('id="delete-stall-form"');
});
