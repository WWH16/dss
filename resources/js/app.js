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
