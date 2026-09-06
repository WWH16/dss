<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Recaptcha implements ValidationRule
{
    /**
     * Expected reCAPTCHA v3 action name (e.g., 'login', 'register', 'evaluation').
     */
    protected ?string $expectedAction;

    /**
     * Minimum score threshold (0.0 = bot, 1.0 = human).
     */
    protected ?float $minScore;

    public function __construct(?string $expectedAction = null, ?float $minScore = null)
    {
        $this->expectedAction = $expectedAction;
        $this->minScore = $minScore;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $secretKey = config('services.recaptcha.secret_key');
        $siteKey = config('services.recaptcha.site_key');

        // Gracefully bypass if reCAPTCHA keys are not configured or when running automated tests
        if (empty($secretKey) || empty($siteKey) || app()->environment('testing')) {
            return;
        }

        if (empty($value) || !is_string($value)) {
            $fail('reCAPTCHA security verification failed. Please refresh and try again.');
            return;
        }

        try {
            $response = Http::asForm()
                ->timeout(6)
                ->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret'   => $secretKey,
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ]);

            if (!$response->successful()) {
                Log::warning('Google reCAPTCHA siteverify request failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                $fail('Unable to contact security verification server. Please try again.');
                return;
            }

            $result = $response->json();
            $success = (bool) ($result['success'] ?? false);
            $score = (float) ($result['score'] ?? 0.0);
            $action = (string) ($result['action'] ?? '');
            $threshold = $this->minScore ?? (float) config('services.recaptcha.min_score', 0.5);

            if (!$success) {
                Log::warning('Google reCAPTCHA verification failed', [
                    'error-codes' => $result['error-codes'] ?? [],
                    'ip'          => request()->ip(),
                ]);
                $fail('reCAPTCHA security check failed. Please refresh the page and try again.');
                return;
            }

            if ($score < $threshold) {
                Log::warning('reCAPTCHA low score detected', [
                    'score'           => $score,
                    'threshold'       => $threshold,
                    'expected_action' => $this->expectedAction,
                    'received_action' => $action,
                    'ip'              => request()->ip(),
                ]);
                $fail('Suspicious automated activity detected. Please try again.');
                return;
            }

            if ($this->expectedAction && $action && $this->expectedAction !== $action) {
                Log::warning('reCAPTCHA action mismatch', [
                    'expected' => $this->expectedAction,
                    'received' => $action,
                    'ip'       => request()->ip(),
                ]);
                $fail('reCAPTCHA action validation mismatch. Please refresh and try again.');
                return;
            }
        } catch (\Throwable $e) {
            Log::error('reCAPTCHA exception: ' . $e->getMessage());
            $fail('Security verification is temporarily unavailable. Please try again.');
        }
    }
}
