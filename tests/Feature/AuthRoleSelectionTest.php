<?php

test('welcome page hero buttons link to appropriate login roles', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('/login?role=student');
    $response->assertSee('/login?role=admin');
    $response->assertSee('id="hero-register-btn"', false);
    $response->assertSee('id="hero-login-btn"', false);
});

test('login page defaults to student role when accessed with role=student', function () {
    $response = $this->get('/login?role=student');

    $response->assertStatus(200);
    $response->assertSee('<option value="student" selected>Student</option>', false);
    $response->assertSee('id="login_student_number_field" style=""', false);
    $response->assertSee('id="login_email_field" style="display: none;"', false);
});

test('login page defaults to admin role when accessed with role=admin', function () {
    $response = $this->get('/login?role=admin');

    $response->assertStatus(200);
    $response->assertSee('<option value="admin" selected>Admin</option>', false);
    $response->assertSee('id="login_student_number_field" style="display: none;"', false);
    $response->assertSee('id="login_email_field" style=""', false);
});

test('login page defaults to staff role when accessed with role=staff', function () {
    $response = $this->get('/login?role=staff');

    $response->assertStatus(200);
    $response->assertSee('<option value="staff" selected>Staff</option>', false);
    $response->assertSee('id="login_student_number_field" style="display: none;"', false);
    $response->assertSee('id="login_email_field" style=""', false);
});

test('auth dropdowns render custom chevron icons and appearance-none styling', function () {
    $response = $this->get('/login');

    $response->assertStatus(200);
    $response->assertSee('id="login_role"', false);
    $response->assertSee('appearance-none', false);
    $response->assertSee('name="chevron-down-outline"', false);
});
