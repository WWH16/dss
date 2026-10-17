{{-- ADDED: the one back link every page uses (login, password, OTP, evaluation, stall QR), so the control looks and reads the same everywhere. Needs $href and $label (name the destination, e.g. "Back to Login"); $class adds classes such as no-print. 44px tall for touch. --}}
<a href="{{ $href }}" class="inline-flex items-center gap-1.5 min-h-11 px-3.5 rounded-md border border-neutral-200 bg-white text-xs font-bold text-neutral-600 hover:text-brand-700 hover:border-neutral-300 shadow-2xs transition-colors {{ $class ?? '' }}">
    <ion-icon name="arrow-back-outline" class="text-sm shrink-0" aria-hidden="true"></ion-icon>
    {{ $label }}
</a>
