// CHANGED: removed the axios bootstrap import; every request uses fetch().
import './toast';

// ADDED: a wrapper with data-track-field="<program select id>" holds the track select. It stays disabled until a program is picked, lists only that program's optgroup, and clears a track from another program.
document.querySelectorAll('[data-track-field]').forEach(field => {
    const course = document.getElementById(field.dataset.trackField);
    const track = field.querySelector('select');
    const sync = () => {
        track.disabled = course.value === '';
        track.querySelectorAll('optgroup').forEach(group => {
            group.hidden = group.disabled = group.label !== course.value;
        });
        if (track.selectedOptions[0]?.parentElement.disabled) track.value = '';
    };
    course.addEventListener('change', sync);
    sync();
});

// ADDED: an <input data-live-search> submits its GET form 450ms after typing stops (the users page's logic, now shared), so a search no longer waits for Enter. After the reload, focus and the caret go back to the end of the box so typing can continue.
document.querySelectorAll('[data-live-search]').forEach(input => {
    let timer;
    input.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => input.form.submit(), 450);
    });
    if (input.value) {
        input.focus();
        input.setSelectionRange(input.value.length, input.value.length);
    }
});

// ADDED: the eye button beside a password field shows or hides it (moved from the login page, shared with the reset password page).
// ??= keeps the admin users page's own togglePasswordVisibility(inputId, btn), which takes the button instead of an icon id.
window.togglePasswordVisibility ??= (fieldId, iconId) => {
    const input = document.getElementById(fieldId);
    const show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    document.getElementById(iconId).setAttribute('name', show ? 'eye-off-outline' : 'eye-outline');
};

// ADDED: a [data-password-rules] checklist (resources/views/partials/password-rules.blade.php) turns each row green
// with a check once the form's password meets that rule; the same four checks as the register form and the server rule.
const passwordRules = {
    length: v => v.length >= 8,
    case: v => /[a-z]/.test(v) && /[A-Z]/.test(v),
    number: v => /[0-9]/.test(v),
    symbol: v => /[^A-Za-z0-9]/.test(v),
};
document.querySelectorAll('[data-password-rules]').forEach(list => {
    const password = list.closest('form').querySelector('[name="password"]');
    password.addEventListener('input', () => list.querySelectorAll('[data-rule]').forEach(row => {
        const ok = passwordRules[row.dataset.rule](password.value);
        const icon = row.querySelector('ion-icon');
        row.classList.toggle('text-neutral-500', !ok);
        row.classList.toggle('text-emerald-600', ok);
        row.classList.toggle('font-bold', ok);
        icon.classList.toggle('text-neutral-400', !ok);
        icon.classList.toggle('text-emerald-500', ok);
        icon.classList.toggle('animate-icon-pop', ok);
        icon.setAttribute('name', ok ? 'checkmark-circle' : 'ellipse-outline');
    }));
});

// ADDED: a <p data-password-match> under a confirm field says live whether it matches the form's password, and a mismatch blocks submit with the browser's own message, so a typo is caught before the server sends the form back with both password fields wiped. While the confirm text is still the start of the password and the field has focus, nothing shows yet.
document.querySelectorAll('[data-password-match]').forEach(hint => {
    const form = hint.closest('form');
    const password = form.querySelector('[name="password"]');
    const confirm = form.querySelector('[name="password_confirmation"]');
    const sync = () => {
        const a = password.value, b = confirm.value, ok = a === b;
        confirm.setCustomValidity(b && !ok ? 'Passwords do not match.' : '');
        confirm.toggleAttribute('aria-invalid', !!b && !ok);
        hint.hidden = !b || (!ok && document.activeElement === confirm && a.startsWith(b));
        hint.dataset.match = ok ? 'yes' : 'no';
        hint.innerHTML = `<ion-icon name="${ok ? 'checkmark-circle' : 'alert-circle'}" class="text-sm leading-none shrink-0" aria-hidden="true"></ion-icon><span>${ok ? 'Passwords match' : 'Passwords do not match'}</span>`;
    };
    form.addEventListener('input', sync);
    confirm.addEventListener('blur', sync);
    form.addEventListener('reset', () => setTimeout(sync));
});
