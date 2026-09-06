<?php

namespace Tests\Unit;

use App\Rules\Recaptcha;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RecaptchaTest extends TestCase
{
    public function test_it_bypasses_when_keys_are_not_configured(): void
    {
        config([
            'services.recaptcha.site_key' => null,
            'services.recaptcha.secret_key' => null,
        ]);

        $rule = new Recaptcha('login');
        $failed = false;

        $rule->validate('g_recaptcha_response', null, function () use (&$failed) {
            $failed = true;
        });

        $this->assertFalse($failed, 'Rule should bypass validation when keys are null');
    }

    public function test_it_verifies_successfully_with_google_api(): void
    {
        config([
            'services.recaptcha.site_key' => 'dummy-site-key',
            'services.recaptcha.secret_key' => 'dummy-secret-key',
            'services.recaptcha.min_score' => 0.5,
        ]);

        // Mock app environment not testing to test real execution
        $this->app->detectEnvironment(fn () => 'production');

        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.9,
                'action' => 'login',
            ], 200),
        ]);

        $rule = new Recaptcha('login');
        $failed = false;

        $rule->validate('g_recaptcha_response', 'valid-token', function () use (&$failed) {
            $failed = true;
        });

        $this->assertFalse($failed, 'Rule should succeed with valid token and high score');
    }

    public function test_it_fails_when_score_is_too_low(): void
    {
        config([
            'services.recaptcha.site_key' => 'dummy-site-key',
            'services.recaptcha.secret_key' => 'dummy-secret-key',
            'services.recaptcha.min_score' => 0.7,
        ]);

        $this->app->detectEnvironment(fn () => 'production');

        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.3,
                'action' => 'login',
            ], 200),
        ]);

        $rule = new Recaptcha('login');
        $errorMessage = '';

        $rule->validate('g_recaptcha_response', 'bot-token', function ($msg) use (&$errorMessage) {
            $errorMessage = $msg;
        });

        $this->assertNotEmpty($errorMessage);
        $this->assertStringContainsString('Suspicious automated activity', $errorMessage);
    }

    public function test_it_fails_when_action_mismatches(): void
    {
        config([
            'services.recaptcha.site_key' => 'dummy-site-key',
            'services.recaptcha.secret_key' => 'dummy-secret-key',
            'services.recaptcha.min_score' => 0.5,
        ]);

        $this->app->detectEnvironment(fn () => 'production');

        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response([
                'success' => true,
                'score' => 0.9,
                'action' => 'other_action',
            ], 200),
        ]);

        $rule = new Recaptcha('login');
        $errorMessage = '';

        $rule->validate('g_recaptcha_response', 'token', function ($msg) use (&$errorMessage) {
            $errorMessage = $msg;
        });

        $this->assertNotEmpty($errorMessage);
        $this->assertStringContainsString('action validation mismatch', $errorMessage);
    }
}
