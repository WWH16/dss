{{-- The register form's 2x2 rule checklist, shared by the reset password page and the profile password modals.
     resources/js/app.js turns a row green once the form's password field meets that rule. --}}
<div data-password-rules class="grid grid-cols-2 gap-x-4 gap-y-0 mt-1.5 font-medium">
    @foreach (['length' => '8+ characters', 'case' => 'Upper & lowercase', 'number' => 'One number', 'symbol' => 'One symbol'] as $rule => $label)
        <div data-rule="{{ $rule }}" class="flex items-center gap-1 text-neutral-500 transition-colors duration-300">
            <ion-icon name="ellipse-outline" class="text-[12px] leading-none text-neutral-400 transition-colors duration-300"></ion-icon>
            <span class="text-[9px] whitespace-nowrap">{{ $label }}</span>
        </div>
    @endforeach
</div>
